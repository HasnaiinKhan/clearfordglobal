<x-mail::message>
# Thanks for reaching out, {{ $inquiry->name }}.

We've received your inquiry and a member of our team will be in touch within 1–2 business days.

---

Here's a summary of what you submitted:

**Company:** {{ $inquiry->company }}

**Service of interest:** {{ $inquiry->service }}

---

If you need to reach us sooner, reply directly to this email.

We look forward to learning more about your project.

**The Clearford Global Team**

*Please do not reply to this message if you did not submit this inquiry.*
</x-mail::message>
