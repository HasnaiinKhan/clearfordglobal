# Clearford Global Website Design

## Overview

This document describes the technical design for the Clearford Global marketing website. The site is a single-page Laravel 12 application that introduces Clearford's services, explains their engagement process, and provides a secure project inquiry form.

The design is intentionally minimal: one public route renders the homepage, one POST route handles form submission, and one route serves the sitemap. All content is config-driven so copy can be updated without touching Blade templates. Security, accessibility, and performance are first-class concerns.

---

## Glossary

| Term                          | Definition                                                                                                                                    |
| ----------------------------- | --------------------------------------------------------------------------------------------------------------------------------------------- |
| **ContactFormRequest**        | A Laravel Form Request class that centralises all server-side validation rules for the inquiry form.                                          |
| **RecaptchaService**          | A single-responsibility service class that wraps the Google reCAPTCHA v3 API call and score evaluation.                                       |
| **InquiryNotification**       | A queued Mailable that sends the team notification email to `INQUIRY_TO_ADDRESS`.                                                             |
| **InquiryAutoReply**          | An optional queued Mailable that sends a confirmation email to the submitter.                                                                 |
| **SecurityHeadersMiddleware** | Middleware applied globally to inject security-related HTTP response headers on every request.                                                |
| **Inquiry**                   | The Eloquent model and database record representing a submitted project inquiry.                                                              |
| **config/clearford.php**      | The single source of truth for all site content (services, process steps, SEO data, company details).                                         |
| **Alpine.js component**       | An `x-data` scope in a Blade template that manages reactive UI state client-side (mobile nav, contact form).                                  |
| **Honeypot**                  | A hidden form field that must remain empty; a populated value signals automated bot activity.                                                 |
| **Deduplication window**      | A configurable time range (default 24 hours) within which a second submission from the same email with identical project details is rejected. |
| **Bug_Condition (C)**         | Not applicable - this is a greenfield feature spec, not a bugfix.                                                                             |
| **CSP nonce**                 | Not used in this design; CSP allows domains rather than nonces to keep implementation simple on shared hosting.                               |

---

## Architecture

```
Browser
  │
  ├─ GET /          → web.php → HomeController@index → home.blade.php
  ├─ POST /contact  → web.php → ContactController@store (rate-limited)
  └─ GET /sitemap.xml → web.php → SitemapController@index
         │
         ├─ ContactFormRequest (validation, honeypot, timestamp, reCAPTCHA)
         ├─ RecaptchaService (Google API call)
         ├─ Inquiry model (SQLite)
         └─ Mail queue → InquiryNotification + InquiryAutoReply
```

The application has no authentication, no admin panel, and no JavaScript framework beyond Alpine.js. All pages are server-rendered Blade templates; Alpine.js only enhances progressive interactivity.

---

## File Structure

### Routes - `routes/web.php`

```php
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:contact')
    ->name('contact.store');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
```

The `throttle:contact` middleware alias maps to the named rate limiter defined in `bootstrap/app.php`.

### Controllers

| File                                         | Responsibility                                                                                             |
| -------------------------------------------- | ---------------------------------------------------------------------------------------------------------- |
| `app/Http/Controllers/HomeController.php`    | Passes config data to the `home` view.                                                                     |
| `app/Http/Controllers/ContactController.php` | Handles form POST: validates, deduplicates, saves Inquiry, dispatches mail jobs, returns JSON or redirect. |
| `app/Http/Controllers/SitemapController.php` | Returns a simple XML sitemap response.                                                                     |

> **Note:** The base `app/Http/Controllers/Controller.php` already exists. `HomeController` and `SitemapController` are thin; they can remain in the same file namespace without further extraction.

### Form Request

**`app/Http/Requests/ContactFormRequest.php`**

Centralises all validation rules. The `authorize()` method returns `true` (public form). Rules described in detail in the [Contact Form section](#5-contact--project-inquiry-form).

### Middleware

**`app/Http/Middleware/SecurityHeadersMiddleware.php`**

Injects security headers on every response. Registered globally in `bootstrap/app.php`. Detailed header values are listed in the [Security Headers section](#security-headers-middleware).

### Models

**`app/Models/Inquiry.php`**

Eloquent model mapping to the `inquiries` table. `$fillable` covers all form fields plus metadata columns. No relationships required.

### Mail

| File                               | Description                                                                                         |
| ---------------------------------- | --------------------------------------------------------------------------------------------------- |
| `app/Mail/InquiryNotification.php` | Team notification. Implements `ShouldQueue`.                                                        |
| `app/Mail/InquiryAutoReply.php`    | Submitter confirmation. Implements `ShouldQueue`. Sent only when `INQUIRY_AUTO_REPLY_ENABLED=true`. |

### Services

**`app/Services/RecaptchaService.php`**

Single public method `verify(string $token): float` - calls the Google siteverify endpoint, returns the score, throws or returns `0.0` on failure.

### Configuration

**`config/clearford.php`**

Single config file that acts as the content management layer. Keys:

```php
return [
    'company' => [
        'name'    => 'Clearford Global',
        'email'   => env('INQUIRY_TO_ADDRESS', 'hello@clearford.com'), // placeholder
        'url'     => env('APP_URL'),
    ],
    'seo' => [
        'title'       => 'Clearford Global - Software, Analytics & Healthcare Technology',
        'description' => '...',
        'og_image'    => '/logo/Logowithbg.jpeg',
    ],
    'services' => [
        // array of service definitions - see Services section
    ],
    'process' => [
        // array of step definitions - see How We Work section
    ],
];
```

### Database

**`database/migrations/xxxx_create_inquiries_table.php`**

Schema described in the [Database Schema section](#database-schema).

### Views

```
resources/views/
├── components/
│   ├── layout.blade.php          ← HTML shell, <head>, skip link, JSON-LD
│   ├── service-card.blade.php
│   ├── process-step.blade.php
│   ├── btn-primary.blade.php
│   ├── btn-secondary.blade.php
│   └── section-header.blade.php
├── home.blade.php                ← Full single-page view
└── emails/
    ├── inquiry-notification.blade.php
    └── inquiry-auto-reply.blade.php
```

---

## Components and Interfaces

### `<x-layout>`

The HTML shell. Accepts slots and props:

| Prop          | Type   | Description                                                       |
| ------------- | ------ | ----------------------------------------------------------------- |
| `title`       | string | `<title>` tag value (defaults to `config('clearford.seo.title')`) |
| `description` | string | `<meta name="description">`                                       |

Renders in order:

1. `<!DOCTYPE html>` and opening `<html lang="en">`
2. `<head>`: charset, viewport, title, meta description, Open Graph tags, Twitter Card tags, Organization JSON-LD, favicon links, Vite CSS/JS tags
3. Skip-navigation link (`<a href="#main-content" class="sr-only focus:not-sr-only ...">Skip to main content</a>`) as the first focusable element
4. `<header>` (sticky nav) with Alpine.js mobile menu component
5. `<main id="main-content">{{ $slot }}</main>`
6. `<footer>`

### `<x-service-card>`

Props: `icon` (SVG string), `title`, `description`, `deliverables` (array).

Renders a `<article>` with role semantics, the SVG icon, title as `<h3>`, description paragraph, and a `<ul>` of deliverables.

### `<x-process-step>`

Props: `number` (int), `title`, `copy`.

Renders a visually numbered step block. Number displayed as large decorative text; title as `<h3>`; copy as `<p>`.

### `<x-btn-primary>` / `<x-btn-secondary>`

Props: `href` (optional - renders `<a>` if provided, `<button>` otherwise), `type` (default `button`).

Using `<x-slot:default>` for label text. Encapsulates focus ring and hover state Tailwind classes to avoid duplication.

### `<x-section-header>`

Props: `title`, `subtitle` (optional).

Renders section heading as `<h2>` with optional subtitle paragraph. Consistent spacing applied via component.

---

## Tailwind CSS v4 Configuration

All brand tokens are defined in `resources/css/app.css` using the Tailwind v4 `@theme` directive. No separate `tailwind.config.js` is needed.

```css
@import "tailwindcss";

@theme {
    --color-navy: #1b2a4a; /* PENDING CLIENT CONFIRMATION */
    --color-teal: #2aafa0; /* PENDING CLIENT CONFIRMATION */
    --color-white: #ffffff;
    --font-sans: "Inter", ui-sans-serif, system-ui, sans-serif;
}

@layer components {
    .form-input {
        @apply w-full rounded-md border border-gray-300 px-3 py-2 text-sm
           focus:outline-none focus:ring-2 focus:ring-teal focus:border-teal
           disabled:opacity-50 disabled:cursor-not-allowed;
    }
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
}
```

**Font choice**: Inter via [Bunny Fonts](https://fonts.bunny.net) (privacy-respecting alternative to Google Fonts). **FLAGGED FOR CLIENT APPROVAL.**

> The `--color-navy` and `--color-teal` values are extracted from the logo and are pending client confirmation (Requirements 16.1.1). Tokens are centralised here so a single change propagates everywhere.

---

## Alpine.js Components

### Mobile Navigation - `x-data="mobileNav()"`

Defined inline in `layout.blade.php` or extracted to `resources/js/components/mobile-nav.js`.

```js
function mobileNav() {
    return {
        open: false,
        toggle() {
            this.open = !this.open;
        },
        close() {
            this.open = false;
        },
        handleEscape(e) {
            if (e.key === "Escape") this.close();
        },
    };
}
```

Template bindings:

- Hamburger `<button>`: `:aria-expanded="open"`, `@click="toggle()"`, `aria-label="Open navigation menu"` / `aria-label="Close navigation menu"` (toggled via `:aria-label`)
- Nav drawer: `x-show="open"`, `@keydown.escape.window="handleEscape($event)"`, `x-trap="open"` (Alpine Focus plugin for focus trap - **dependency to add**)
- Each nav link: `@click="close()"` to dismiss menu on selection

### Contact Form - `x-data="contactForm()"`

Defined in `resources/js/components/contact-form.js` and bundled via Vite.

```js
function contactForm() {
    return {
        fields: { name: "", company: "", email: "", service: "", details: "" },
        errors: {},
        loading: false,
        success: false,
        generalError: "",
        formRenderedAt: Date.now(),

        async submit() {
            this.loading = true;
            this.errors = {};
            this.generalError = "";

            // Inject reCAPTCHA token
            const token = await grecaptcha.execute(RECAPTCHA_SITE_KEY, {
                action: "contact",
            });

            const payload = {
                ...this.fields,
                recaptcha_token: token,
                _form_rendered_at: this.formRenderedAt,
            };

            try {
                const res = await fetch("/contact", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]',
                        ).content,
                        Accept: "application/json",
                    },
                    body: JSON.stringify(payload),
                });

                const data = await res.json();

                if (res.ok) {
                    this.success = true;
                } else if (res.status === 422) {
                    this.errors = data.errors;
                } else {
                    this.generalError =
                        data.message ??
                        "Something went wrong. Please try again.";
                }
            } catch {
                this.generalError =
                    "Could not connect. Please check your connection and try again.";
            } finally {
                this.loading = false;
            }
        },
    };
}
```

`RECAPTCHA_SITE_KEY` is printed into the page via a Blade expression:

```html
<script>
    const RECAPTCHA_SITE_KEY = '{{ config('services.recaptcha.site_key') }}';
</script>
```

The reCAPTCHA script tag is loaded in `<head>`:

```html
<script
    src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"
    async
    defer
></script>
```

---

## 5. Contact / Project Inquiry Form

### Server-Side Flow

```
POST /contact
  │
  ├─ SecurityHeadersMiddleware
  ├─ throttle:contact (5 req/hr per IP → 429)
  │
  ├─ ContactFormRequest::authorize() → true
  ├─ ContactFormRequest::rules()
  │     ├─ name:     required|string|max:255
  │     ├─ company:  required|string|max:255
  │     ├─ email:    required|email:rfc,dns|max:255
  │     ├─ service:  required|in:[valid options]
  │     ├─ details:  required|string|min:20|max:2000
  │     ├─ website:  prohibited (honeypot - field name must be non-obvious)
  │     ├─ _form_rendered_at: required|integer (checked in controller)
  │     └─ recaptcha_token: required|string
  │
  ├─ ContactController::store()
  │     ├─ Timestamp check: (now - _form_rendered_at) < 3s → reject (configurable)
  │     ├─ RecaptchaService::verify($token) → score < 0.5 → reject
  │     ├─ Deduplication: Inquiry::where(email, details, within window)→exists() → reject
  │     ├─ Inquiry::create([...sanitised fields, ip, ua, recaptcha_score])
  │     ├─ InquiryNotification::dispatch($inquiry)
  │     ├─ if INQUIRY_AUTO_REPLY_ENABLED: InquiryAutoReply::dispatch($inquiry)
  │     └─ return JSON {success:true} (AJAX) OR redirect()->back()->with('success', ...) (no-JS)
  │
  └─ On validation failure:
        AJAX: 422 JSON {errors: {...}}
        No-JS: redirect()->back()->withErrors()->withInput()
```

### ContactFormRequest - Validation Rules Detail

```php
public function rules(): array
{
    return [
        'name'               => ['required', 'string', 'max:255'],
        'company'            => ['required', 'string', 'max:255'],
        'email'              => ['required', 'email:rfc,dns', 'max:255'],
        'service'            => ['required', Rule::in([
                                    'Software Development',
                                    'Data Analytics & Reporting',
                                    'Software QA & Testing',
                                    'Business Analysis & Operations',
                                    'Healthcare Technology & Operations',
                                    'Not sure yet',
                                ])],
        'details'            => ['required', 'string', 'min:20', 'max:2000'],
        'website'            => ['prohibited'],          // honeypot
        '_form_rendered_at'  => ['required', 'integer'],
        'recaptcha_token'    => ['required', 'string'],
    ];
}
```

The honeypot field `website` is rendered as `<input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">` in the Blade form. The field name is intentionally plausible to attract bots.

### Timestamp Check

Performed in `ContactController::store()` after form request validation passes:

```php
$renderedAt = (int) $request->input('_form_rendered_at');
$elapsed    = (time() * 1000) - $renderedAt; // milliseconds
$minMs      = config('clearford.form.min_submission_ms', 3000);

if ($elapsed < $minMs) {
    return $this->rejectSpam($request, 'Submission too fast.');
}
```

### RecaptchaService

```php
class RecaptchaService
{
    public function verify(string $token): float
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret'),
            'response' => $token,
            'remoteip' => request()->ip(),
        ]);

        if (! $response->successful()) {
            Log::warning('reCAPTCHA API request failed.', ['status' => $response->status()]);
            return 0.0;
        }

        $body = $response->json();

        if (! ($body['success'] ?? false)) {
            return 0.0;
        }

        return (float) ($body['score'] ?? 0.0);
    }
}
```

Threshold: `0.5` (configurable via `config('clearford.recaptcha.threshold', 0.5)`).

### Deduplication

```php
$duplicate = Inquiry::where('email', $validated['email'])
    ->where('details', $validated['details'])
    ->where('created_at', '>=', now()->subHours(
        config('clearford.form.dedup_hours', 24)
    ))
    ->exists();

if ($duplicate) {
    return $this->rejectDuplicate($request);
}
```

### AJAX vs No-JS Responses

The controller detects the request type via `$request->expectsJson()`:

```php
// Success
if ($request->expectsJson()) {
    return response()->json(['success' => true]);
}
return redirect()->back()->with('inquiry_success', true);

// Validation failure (handled by Form Request automatically for no-JS)
// For AJAX, Laravel returns 422 with JSON errors automatically when Accept: application/json.

// Spam/reCAPTCHA rejection
private function rejectSpam(Request $request, string $reason): JsonResponse|RedirectResponse
{
    Log::info('Spam submission rejected.', ['reason' => $reason, 'ip' => $request->ip()]);

    if ($request->expectsJson()) {
        return response()->json(['message' => 'Your submission could not be processed.'], 422);
    }
    return redirect()->back()->with('inquiry_error', 'Your submission could not be processed.');
}
```

---

## Security Headers Middleware

**`app/Http/Middleware/SecurityHeadersMiddleware.php`**

Applied in `bootstrap/app.php` as a global middleware via `$middleware->append(SecurityHeadersMiddleware::class)`.

| Header                      | Value                                                                                                                                                                                                                                                 |
| --------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `Content-Security-Policy`   | `default-src 'self'; script-src 'self' https://www.google.com https://www.gstatic.com; style-src 'self' https://fonts.bunny.net; font-src 'self' https://fonts.bunny.net; img-src 'self' data:; frame-src https://www.google.com; connect-src 'self'` |
| `X-Frame-Options`           | `DENY`                                                                                                                                                                                                                                                |
| `X-Content-Type-Options`    | `nosniff`                                                                                                                                                                                                                                             |
| `Referrer-Policy`           | `strict-origin-when-cross-origin`                                                                                                                                                                                                                     |
| `Permissions-Policy`        | `camera=(), microphone=(), geolocation=()`                                                                                                                                                                                                            |
| `Strict-Transport-Security` | `max-age=31536000; includeSubDomains` (production only - gated by `app()->isProduction()`)                                                                                                                                                            |

> **Open item**: CSP `script-src` and `frame-src` include `www.google.com` for reCAPTCHA. If reCAPTCHA is not approved (Requirements 16.1.2), these directives can be tightened. CSP may also need adjustment when the real production domain is known.

---

## Data Models

**Migration**: `database/migrations/xxxx_create_inquiries_table.php`

```php
Schema::create('inquiries', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('company');
    $table->string('email');
    $table->string('service');
    $table->text('details');
    $table->string('ip_address', 45)->nullable();   // IPv4 and IPv6
    $table->text('user_agent')->nullable();
    $table->decimal('recaptcha_score', 3, 2)->nullable();
    $table->timestamps();

    // Index for deduplication query
    $table->index(['email', 'created_at']);
});
```

**Inquiry model** `$fillable`:

```php
protected $fillable = [
    'name', 'company', 'email', 'service',
    'details', 'ip_address', 'user_agent', 'recaptcha_score',
];
```

No sensitive encryption is applied to stored fields beyond Laravel's standard escaping. The `ip_address` column uses VARCHAR(45) to accommodate IPv6 addresses.

---

## Mail Design

### InquiryNotification

| Property | Value                                                                            |
| -------- | -------------------------------------------------------------------------------- |
| From     | `config('mail.from.address')` / `config('mail.from.name')` - never the submitter |
| Reply-To | Submitter's `email` from the inquiry                                             |
| To       | `env('INQUIRY_TO_ADDRESS')`                                                      |
| Subject  | `"New Project Inquiry from {$inquiry->name} – {$inquiry->company}"`              |
| Queue    | `default` queue (implements `ShouldQueue`)                                       |

Body content: all inquiry fields displayed in a plain, readable layout. Blade template at `resources/views/emails/inquiry-notification.blade.php`.

### InquiryAutoReply

| Property    | Value                                                               |
| ----------- | ------------------------------------------------------------------- |
| From        | `config('mail.from.address')` / `config('mail.from.name')`          |
| To          | Submitter's `email` from the inquiry                                |
| Subject     | `"We received your inquiry – Clearford Global"`                     |
| Queue       | `default` queue (implements `ShouldQueue`)                          |
| Conditional | Only dispatched when `env('INQUIRY_AUTO_REPLY_ENABLED') === 'true'` |

Body: warm, professional confirmation. Avoids unconfirmed promises or timelines. Blade template at `resources/views/emails/inquiry-auto-reply.blade.php`.

### Mail Failure Logging

Both Mailables implement `failed(Throwable $e)`:

```php
public function failed(Throwable $e): void
{
    Log::error('Mail job failed.', [
        'mailable' => static::class,
        'inquiry_id' => $this->inquiry->id,
        'error' => $e->getMessage(),
    ]);
}
```

---

## Rate Limiting

Defined in `bootstrap/app.php` using `RateLimiter::for`:

```php
RateLimiter::for('contact', function (Request $request) {
    return Limit::perHour(5)->by($request->ip())->response(function () use ($request) {
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Too many requests. Please try again later.'], 429);
        }
        return redirect()->back()->with('inquiry_error', 'Too many requests. Please try again later.');
    });
});
```

Applied to the `POST /contact` route via `->middleware('throttle:contact')`.

---

## SEO Assets

### Organization JSON-LD

Embedded in `<x-layout>` head. Rendered via a Blade partial `resources/views/partials/json-ld.blade.php`:

```json
{
    "@context": "https://schema.org",
    "@type": "Organization",
    "name": "Clearford Global",
    "url": "{{ config('app.url') }}",
    "logo": "{{ config('app.url') }}/logo/Logowithoutbg.png",
    "contactPoint": {
        "@type": "ContactPoint",
        "contactType": "customer support",
        "email": "{{ config('clearford.company.email') }}"
    }
}
```

### Open Graph & Twitter Card

In `<x-layout>` head:

```html
<meta
    property="og:title"
    content="{{ $title ?? config('clearford.seo.title') }}"
/>
<meta
    property="og:description"
    content="{{ $description ?? config('clearford.seo.description') }}"
/>
<meta
    property="og:image"
    content="{{ config('app.url') . config('clearford.seo.og_image') }}"
/>
<meta property="og:url" content="{{ config('app.url') }}" />
<meta property="og:type" content="website" />

<meta name="twitter:card" content="summary_large_image" />
<meta
    name="twitter:title"
    content="{{ $title ?? config('clearford.seo.title') }}"
/>
<meta
    name="twitter:description"
    content="{{ $description ?? config('clearford.seo.description') }}"
/>
<meta
    name="twitter:image"
    content="{{ config('app.url') . config('clearford.seo.og_image') }}"
/>
```

### Sitemap

`SitemapController@index` returns an XML response:

```php
return response()->view('sitemap', ['url' => config('app.url')])
    ->header('Content-Type', 'application/xml');
```

The sitemap contains a single `<url>` entry for the homepage (`/`) with `<changefreq>monthly</changefreq>`.

### robots.txt

Static file at `public/robots.txt`:

```
User-agent: *
Allow: /
Sitemap: https://clearfordglobal.com/sitemap.xml
```

> The domain in `robots.txt` should be updated when the production domain is confirmed.

### Favicon

`public/favicon.ico` already exists. Additionally:

```html
<link rel="icon" href="/favicon.ico" sizes="any" />
<link
    rel="icon"
    href="/logo/Logowithoutbg.png"
    type="image/png"
    sizes="192x192"
/>
<link rel="apple-touch-icon" href="/logo/Logowithoutbg.png" />
```

> A properly sized 192×192 PNG and an `apple-touch-icon` (180×180) should be generated from the logo before launch.

---

## Vite Configuration

**`vite.config.js`** (update from default):

```js
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

`resources/js/app.js` imports Alpine.js and registers components:

```js
import Alpine from "alpinejs";
import focus from "@alpinejs/focus"; // for focus trap in mobile nav

import mobileNav from "./components/mobile-nav.js";
import contactForm from "./components/contact-form.js";

Alpine.plugin(focus);
Alpine.data("mobileNav", mobileNav);
Alpine.data("contactForm", contactForm);

Alpine.start();
```

---

## Environment Variables

All required env keys with descriptions. Document in `.env.example`:

```dotenv
# Application
APP_NAME="Clearford Global"
APP_URL=http://localhost
APP_ENV=local          # production on live
APP_DEBUG=true         # false on production
APP_KEY=               # generated via php artisan key:generate

# Database
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite

# Mail (Hostinger SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=           # your Hostinger email address
MAIL_PASSWORD=           # your Hostinger email password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=       # sender address (e.g. noreply@clearfordglobal.com)
MAIL_FROM_NAME="${APP_NAME}"

# Inquiry routing
INQUIRY_TO_ADDRESS=      # PLACEHOLDER - destination inbox, pending client confirmation
INQUIRY_AUTO_REPLY_ENABLED=false

# reCAPTCHA v3 - PENDING CLIENT CONFIRMATION (Requirements 16.1.2)
RECAPTCHA_SITE_KEY=
RECAPTCHA_SECRET_KEY=

# Queue driver
# "database" is recommended for Hostinger shared hosting (no Supervisor required at low volume).
# Switch to "redis" or use Supervisor + "database" on a VPS for higher throughput.
QUEUE_CONNECTION=database
```

> **Open item**: `QUEUE_CONNECTION=database` is recommended for Hostinger shared hosting because it requires no additional infrastructure. On a VPS with Supervisor available, `database` with a Supervisor-managed queue worker provides better reliability. Redis queues are preferable if the hosting environment supports it.

---

## Error Handling

### Validation Errors (422)

`ContactFormRequest` automatically returns a 422 response for AJAX requests (`Accept: application/json`) with a JSON body shaped as `{"message": "...", "errors": {"field": ["message"]}}`. For standard form submissions it redirects back with `withErrors()` and `withInput()` so the form repopulates.

### Spam / Bot Rejections (422)

Honeypot, timestamp, reCAPTCHA, and deduplication failures are handled in `ContactController` and return a plain `{"message": "Your submission could not be processed."}` with status 422 for AJAX, or a redirect with a flash message for standard submissions. Internal rejection reasons are never exposed to the client.

### Rate Limit Exceeded (429)

The named limiter's response callback returns `{"message": "Too many requests. Please try again later."}` with status 429 for AJAX, or a redirect with a flash error for standard submissions.

### General Server Errors (500)

Laravel's default exception handler is used. In production (`APP_DEBUG=false`) all 500 errors render a generic error page with no stack trace. The Alpine.js form component catches fetch errors and displays a user-friendly message. Mail job failures are caught by the `failed()` method on each Mailable and written to the Laravel log channel.

### reCAPTCHA API Failure

If the Google siteverify endpoint is unreachable, `RecaptchaService::verify()` returns `0.0` and logs a warning. The submission is treated as a failed reCAPTCHA check and rejected. This is a conservative fail-closed approach.

---

## Testing Strategy

### Test File

`tests/Feature/ContactFormTest.php`

### Test Cases

| #   | Scenario                                       | Assertions                                                                                                                                                                          |
| --- | ---------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 1   | Happy path - valid submission (AJAX)           | `assertDatabaseHas('inquiries', [...])`, `Mail::assertQueued(InquiryNotification::class)`, `assertStatus(200)`, JSON `success: true`                                                |
| 2   | Happy path - auto-reply enabled                | `Mail::assertQueued(InquiryAutoReply::class)`                                                                                                                                       |
| 3   | Auto-reply disabled                            | `Mail::assertNotQueued(InquiryAutoReply::class)`                                                                                                                                    |
| 4   | Missing required field (each field)            | `assertStatus(422)`, JSON `errors` contains field key, `assertDatabaseMissing`                                                                                                      |
| 5   | Email invalid format                           | `assertStatus(422)`, `errors.email` present                                                                                                                                         |
| 6   | Details too short (< 20 chars)                 | `assertStatus(422)`, `errors.details` present                                                                                                                                       |
| 7   | Details too long (> 2000 chars)                | `assertStatus(422)`, `errors.details` present                                                                                                                                       |
| 8   | Invalid service option                         | `assertStatus(422)`, `errors.service` present                                                                                                                                       |
| 9   | Honeypot populated                             | Submission silently rejected (200/422 without DB record)                                                                                                                            |
| 10  | Timestamp too fast (< 3s)                      | Submission rejected, no DB record                                                                                                                                                   |
| 11  | reCAPTCHA score below threshold                | Submission rejected, no DB record                                                                                                                                                   |
| 12  | Rate limit exceeded (6th request from same IP) | `assertStatus(429)`                                                                                                                                                                 |
| 13  | Duplicate submission within window             | Submission rejected, second DB record not created                                                                                                                                   |
| 14  | Notification email: To, Reply-To, Subject      | `Mail::assertQueued(InquiryNotification::class, function ($mail) use ($inquiry) { return $mail->hasTo(config('clearford.company.email')) && $mail->hasReplyTo($inquiry->email); })` |
| 15  | No-JS happy path (standard form POST)          | `assertRedirect()`, session flash contains `inquiry_success`                                                                                                                        |
| 16  | No-JS validation failure                       | `assertRedirect()`, session `errors` populated, old input present                                                                                                                   |

### Test Setup

```php
// ContactFormTest.php - shared setup
beforeEach(function () {
    Mail::fake();
    Queue::fake();
    // Mock RecaptchaService to return passing score by default
    $this->mock(RecaptchaService::class)
         ->shouldReceive('verify')
         ->andReturn(0.9);
});
```

A `InquiryFactory` is defined at `database/factories/InquiryFactory.php` to generate valid submission payloads for reuse across tests.

---

## Page Layout - `home.blade.php`

```
<x-layout>
  <!-- Hero Section (#home) -->
  <section id="home">
    <h1>Build better systems. Run better operations.</h1>
    <!-- intro copy, two CTAs -->
  </section>

  <!-- Services Section (#services) -->
  <section id="services">
    <x-section-header title="Our Services" />
    <div class="grid">
      @foreach (config('clearford.services') as $service)
        <x-service-card :service="$service" />
      @endforeach
    </div>
  </section>

  <!-- How We Work Section (#how-we-work) -->
  <section id="how-we-work">
    <x-section-header title="How We Work" />
    @foreach (config('clearford.process') as $step)
      <x-process-step :step="$step" />
    @endforeach
  </section>

  <!-- Contact Section (#contact) -->
  <section id="contact">
    <x-section-header title="Discuss a Project" />
    <!-- Alpine.js contactForm component -->
    <form x-data="contactForm()" @submit.prevent="submit()">
      <!-- fields, honeypot, hidden timestamp, submit button -->
    </form>
  </section>
</x-layout>
```

Navigation anchor IDs match route fragments: `#home`, `#services`, `#how-we-work`, `#contact`.

---

## Correctness Properties

Property 1: Contact Form Submission - Valid Inquiry Persistence

_For any_ form submission where all required fields are present and valid, the reCAPTCHA score is at or above the threshold, the honeypot field is empty, the submission elapsed time exceeds the minimum, and no duplicate exists within the deduplication window, the system SHALL save exactly one `Inquiry` record to the database, dispatch `InquiryNotification` to the mail queue, and return a success response.

**Validates: Requirements 5.5.1, 5.5.2, 5.6.2, 14.1.1**

Property 2: Spam and Bot Rejection - No Side Effects

_For any_ submission that fails honeypot, timestamp, reCAPTCHA, rate-limit, or deduplication checks, the system SHALL NOT create a database record, SHALL NOT dispatch any mail jobs, and SHALL return a rejection response that does not expose internal implementation details.

**Validates: Requirements 5.3.1, 5.3.2, 5.3.3, 5.3.4, 5.4.1, 14.1.3, 14.1.4, 14.1.5**

Property 3: Preservation - Non-Contact Routes Unaffected

_For any_ request to `GET /` or `GET /sitemap.xml`, the security headers middleware SHALL attach the correct headers and the route SHALL return a 200 response regardless of the state of the inquiries table or mail configuration.

**Validates: Requirements 11.1.2, 10.1.5**

---

## Open Items

The following decisions require client input before implementation is complete:

| #   | Item                                                              | Status                                                                                                      |
| --- | ----------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------- |
| 1   | Brand colours Navy `#1B2A4A` and Teal `#2AAFA0`                   | **PENDING CLIENT CONFIRMATION** (Requirements 16.1.1)                                                       |
| 2   | `INQUIRY_TO_ADDRESS` destination inbox                            | **PLACEHOLDER - pending client confirmation** (Requirements 16.1.3)                                         |
| 3   | Font choice: Inter via Bunny Fonts                                | **FLAGGED FOR CLIENT APPROVAL**                                                                             |
| 4   | Google reCAPTCHA v3 as spam protection mechanism                  | **PENDING CLIENT CONFIRMATION** (Requirements 16.1.2)                                                       |
| 5   | Third-party analytics (none at launch)                            | **PENDING CLIENT CONFIRMATION** (Requirements 16.1.6)                                                       |
| 6   | CSP directives include `www.google.com` for reCAPTCHA             | Adjust once real domain is known and reCAPTCHA decision is confirmed                                        |
| 7   | Queue driver: `database` recommended for Hostinger shared hosting | Note: Supervisor + `database` or Redis preferred on VPS                                                     |
| 8   | Privacy Policy and Terms of Service pages                         | Footer placeholder links included; pages **FLAGGED FOR CLIENT REVIEW** before go-live (Requirements 16.1.5) |
| 9   | Hero section intro copy                                           | Draft placeholder; **REQUIRES CLIENT REVIEW AND APPROVAL** before launch (Requirements 16.1.4)              |
| 10  | Production domain for `robots.txt` and JSON-LD                    | Update once domain is confirmed                                                                             |
| 11  | Favicon 192×192 PNG and 180×180 `apple-touch-icon`                | Should be generated from logo assets before launch                                                          |
