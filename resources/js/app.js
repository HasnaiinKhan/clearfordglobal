import Alpine from "alpinejs";
import focus from "@alpinejs/focus";

window.Alpine = Alpine;
Alpine.plugin(focus);

// ── Mobile Navigation ──────────────────────────────────────────────────────
Alpine.data("mobileNav", () => ({
    open: false,
    toggle() { this.open = !this.open; },
    close()  { this.open = false; },
    handleEscape() { this.open = false; },
}));

// ── Contact Form ───────────────────────────────────────────────────────────
Alpine.data("contactForm", () => ({
    submitting:   false,
    submitted:    false,
    fieldErrors:  {},
    generalError: "",

    async submitForm(form) {
        if (this.submitting) return;

        this.submitting   = true;
        this.fieldErrors  = {};
        this.generalError = "";

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? "";
            const data      = new FormData(form);

            const response = await fetch("/contact", {
                method:  "POST",
                headers: {
                    "X-CSRF-TOKEN":     csrfToken,
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept":           "application/json",
                },
                body: data,
            });

            if (response.status === 419) {
                this.generalError = "Session expired — please refresh the page and try again.";
                return;
            }
            if (response.status === 429) {
                this.generalError = "Too many submissions — please wait a few minutes.";
                return;
            }

            let json;
            try {
                json = await response.json();
            } catch {
                this.generalError = "Unexpected server response. Please try again.";
                return;
            }

            if (response.ok && json.success) {
                this.submitted = true;
                form.reset();
                return;
            }

            if (response.status === 422 && json.errors) {
                for (const [field, messages] of Object.entries(json.errors)) {
                    this.fieldErrors[field] = Array.isArray(messages) ? messages[0] : messages;
                }
                return;
            }

            this.generalError = json.message ?? "Something went wrong. Please try again.";

        } catch (err) {
            console.error("Form error:", err);
            this.generalError = "Network error — please check your connection and try again.";
        } finally {
            this.submitting = false;
        }
    },
}));

Alpine.start();