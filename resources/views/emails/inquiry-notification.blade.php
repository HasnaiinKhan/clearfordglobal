<x-mail::message>
# New Project Inquiry — Clearford Global

You have received a new project inquiry from your website.

---

**Name:** {{ $inquiry->name }}

**Company:** {{ $inquiry->company }}

**Email:** [{{ $inquiry->email }}](mailto:{{ $inquiry->email }})

**Service:** {{ $inquiry->service }}

**Submitted:** {{ $inquiry->created_at->format('F j, Y g:i A') }}

---

**Project Details:**

{{ $inquiry->project_details }}

---

<x-mail::button :url="'mailto:' . $inquiry->email">
Reply to {{ $inquiry->name }}
</x-mail::button>

*Clearford Global — Inquiry Notification*
</x-mail::message>