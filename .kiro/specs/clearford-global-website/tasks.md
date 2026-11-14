# Implementation Plan

## Overview

This document breaks the Clearford Global Website implementation into ten sequential phases, each containing independently verifiable tasks. Phases are ordered so that each one's outputs are available before the next begins: setup and config come first, then database, backend, mail, frontend, JavaScript, SEO, tests, and finally documentation.

## Task Dependency Graph

```json
{
    "waves": [
        {
            "wave": 1,
            "tasks": ["1", "2", "3", "4", "5"],
            "description": "Project setup, dependencies, config, and middleware"
        },
        {
            "wave": 2,
            "tasks": ["6", "7", "8"],
            "description": "Database migration and Inquiry model"
        },
        {
            "wave": 3,
            "tasks": ["9", "10", "11", "12", "13", "18", "19", "20"],
            "description": "Backend contact form, controllers, routes, and Blade layout/design system"
        },
        {
            "wave": 4,
            "tasks": ["14", "15", "16", "17", "21", "22"],
            "description": "Mail mailables, email templates, mail config, and all page sections"
        },
        {
            "wave": 5,
            "tasks": ["23", "24", "25"],
            "description": "Alpine.js components"
        },
        {
            "wave": 6,
            "tasks": ["26", "27"],
            "description": "SEO assets, sitemap, robots.txt, performance attributes"
        },
        {
            "wave": 7,
            "tasks": ["28", "29", "30"],
            "description": "Pest tests"
        },
        {
            "wave": 8,
            "tasks": ["31", "32", "33"],
            "description": "QA, README, deployment checklist, final verification"
        }
    ]
}
```

## Tasks

## Phase 1 - Project Setup & Dependencies

- [ ]   1. Install Alpine.js and configure Vite
    - [ ] 1.1 Install Alpine.js core and focus plugin via npm
        - Run `npm install alpinejs @alpinejs/focus`
        - Confirm packages appear in `package.json` dependencies
        - _Requirements: 1.2.5_

    - [ ] 1.2 Verify `@tailwindcss/vite` plugin is installed and wired into `vite.config.js`
        - Check `vite.config.js` imports `@tailwindcss/vite` and passes it to `plugins`
        - Confirm `resources/css/app.css` starts with `@import "tailwindcss"`
        - Run `npm run build` and confirm no Tailwind errors
        - _Requirements: 7.1.1, 7.1.4_

    - [ ] 1.3 Update `vite.config.js` to import `tailwindcss` from `@tailwindcss/vite`
        - Input array must include `resources/css/app.css` and `resources/js/app.js`
        - Enable `refresh: true` on the Laravel plugin
        - _Requirements: 7.1.1_

- [ ]   2. Install Pest testing framework
    - [ ] 2.1 Install Pest and the Laravel plugin via Composer
        - Run `composer require --dev pestphp/pest pestphp/pest-plugin-laravel`
        - Run `./vendor/bin/pest --init` to generate `tests/Pest.php`
        - Confirm `tests/Pest.php` is created
        - _Requirements: 14.1.1_

- [ ]   3. Create `config/clearford.php` configuration file
    - [ ] 3.1 Create `config/clearford.php` with `company` block
        - Keys: `name`, `email` (from `INQUIRY_TO_ADDRESS` env), `url` (from `APP_URL`)
        - _Requirements: 13.1.3, 15.1.2_

    - [ ] 3.2 Add `seo` block to `config/clearford.php`
        - Keys: `title`, `description`, `og_image`
        - Default title: `"Clearford Global - Software, Analytics & Healthcare Technology"`
        - _Requirements: 10.1.3, 10.1.4_

    - [ ] 3.3 Add `services` array to `config/clearford.php`
        - Five entries: Software Development, Data Analytics & Reporting, Software QA & Testing, Business Analysis & Operations, Healthcare Technology & Operations
        - Each entry has: `icon`, `title`, `description`, `deliverables` (array)
        - _Requirements: 3.2.2, 3.2.3, 13.1.3_

    - [ ] 3.4 Add `process` array to `config/clearford.php`
        - Three entries: scope/deliverables, milestone review, handover with documentation
        - Each entry has: `number`, `title`, `copy`
        - _Requirements: 4.1.1, 4.1.2_

    - [ ] 3.5 Add `form` block to `config/clearford.php`
        - Keys: `min_submission_ms` (default `3000`), `dedup_hours` (default `24`)
        - _Requirements: 5.3.2, 5.4.1_

    - [ ] 3.6 Add `recaptcha` block to `config/clearford.php`
        - Key: `threshold` (default `0.5`)
        - _Requirements: 5.3.3_

- [ ]   4. Update `.env.example` with all required keys
    - [ ] 4.1 Add application keys to `.env.example`
        - `APP_NAME`, `APP_URL`, `APP_ENV`, `APP_DEBUG`, `APP_KEY` with inline comments
        - _Requirements: 15.1.2_

    - [ ] 4.2 Add database keys to `.env.example`
        - `DB_CONNECTION=sqlite`, `DB_DATABASE` with placeholder path
        - _Requirements: 15.1.2_

    - [ ] 4.3 Add mail keys to `.env.example`
        - `MAIL_MAILER`, `MAIL_HOST=smtp.hostinger.com`, `MAIL_PORT=465`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_ENCRYPTION=ssl`, `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`
        - _Requirements: 15.1.2_

    - [ ] 4.4 Add inquiry routing keys to `.env.example`
        - `INQUIRY_TO_ADDRESS` (comment: PLACEHOLDER - pending client confirmation)
        - `INQUIRY_AUTO_REPLY_ENABLED=false`
        - _Requirements: 5.5.2, 5.5.4, 15.1.2_

    - [ ] 4.5 Add reCAPTCHA keys to `.env.example`
        - `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` (comment: PENDING CLIENT CONFIRMATION)
        - _Requirements: 5.3.3, 15.1.2_

    - [ ] 4.6 Add queue key to `.env.example`
        - `QUEUE_CONNECTION=database` with comment explaining Hostinger shared-hosting recommendation
        - _Requirements: 5.5.2, 15.1.2_

- [ ]   5. Register `SecurityHeadersMiddleware` globally
    - [ ] 5.1 Create `app/Http/Middleware/SecurityHeadersMiddleware.php`
        - Inject headers: `Content-Security-Policy`, `X-Frame-Options: DENY`, `X-Content-Type-Options: nosniff`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy`
        - Gate `Strict-Transport-Security` behind `app()->isProduction()`
        - _Requirements: 11.1.2_

    - [ ] 5.2 Append `SecurityHeadersMiddleware` in `bootstrap/app.php`
        - Use `$middleware->append(SecurityHeadersMiddleware::class)`
        - _Requirements: 11.1.2_

---

## Phase 2 - Database & Models

- [ ]   6. Create the `inquiries` database migration
    - [ ] 6.1 Generate migration file
        - Run `php artisan make:migration create_inquiries_table`
        - _Requirements: 5.5.1_

    - [ ] 6.2 Define schema in migration
        - Columns: `id`, `name` (string), `company` (string), `email` (string), `service` (string), `details` (text), `ip_address` (string 45, nullable), `user_agent` (text, nullable), `recaptcha_score` (decimal 3,2, nullable), `timestamps`
        - Add composite index on `['email', 'created_at']` for deduplication query
        - _Requirements: 5.5.1, 5.4.1_

- [ ]   7. Create the `Inquiry` Eloquent model
    - [ ] 7.1 Generate and configure `app/Models/Inquiry.php`
        - Run `php artisan make:model Inquiry`
        - Set `$fillable`: `name`, `company`, `email`, `service`, `details`, `ip_address`, `user_agent`, `recaptcha_score`
        - Add cast: `recaptcha_score` → `float`
        - _Requirements: 5.5.1, 13.1.1_

- [ ]   8. Run migration
    - [ ] 8.1 Execute migration against the SQLite database
        - Run `php artisan migrate`
        - Confirm `inquiries` table is created
        - _Requirements: 5.5.1_

---

## Phase 3 - Backend: Contact Form

- [ ]   9. Create `ContactFormRequest`
    - [ ] 9.1 Generate `app/Http/Requests/ContactFormRequest.php`
        - Run `php artisan make:request ContactFormRequest`
        - Set `authorize()` to return `true`
        - _Requirements: 5.2.1_

    - [ ] 9.2 Define validation rules in `ContactFormRequest`
        - `name`: `required|string|max:255`
        - `company`: `required|string|max:255`
        - `email`: `required|email:rfc,dns|max:255`
        - `service`: `required|Rule::in([six valid service values])`
        - `details`: `required|string|min:20|max:2000`
        - `website`: `prohibited` (honeypot)
        - `_form_rendered_at`: `required|integer`
        - `recaptcha_token`: `required|string`
        - _Requirements: 5.1.1, 5.1.2, 5.2.1, 5.2.2, 5.3.1_

- [ ]   10. Create `RecaptchaService`
    - [ ] 10.1 Create `app/Services/RecaptchaService.php`
        - Single public method `verify(string $token): float`
        - POST to `https://www.google.com/recaptcha/api/siteverify` with `secret`, `response`, `remoteip`
        - Return `(float) $body['score']` on success; return `0.0` and log warning on failure
        - Gracefully return `1.0` (pass-through) if `RECAPTCHA_SECRET_KEY` is not set, to allow local development without keys
        - _Requirements: 5.3.3_

- [ ]   11. Create `ContactController`
    - [ ] 11.1 Generate `app/Http/Controllers/ContactController.php`
        - Run `php artisan make:controller ContactController`
        - _Requirements: 5.5.1_

    - [ ] 11.2 Implement the `store` method
        - Inject `ContactFormRequest` and `RecaptchaService`
        - Timestamp check: reject if `(time() * 1000 - _form_rendered_at) < config('clearford.form.min_submission_ms')`
        - reCAPTCHA check: reject if score below `config('clearford.recaptcha.threshold', 0.5)`
        - Deduplication check: reject if matching email+details exists within `dedup_hours` window
        - On pass: `Inquiry::create([...sanitised fields, ip_address, user_agent, recaptcha_score])`
        - Dispatch `InquiryNotification`
        - Conditionally dispatch `InquiryAutoReply` if `INQUIRY_AUTO_REPLY_ENABLED=true`
        - Return `response()->json(['success' => true])` for AJAX, or `redirect()->back()->with('inquiry_success', true)` for standard POST
        - _Requirements: 5.3.2, 5.3.3, 5.4.1, 5.5.1, 5.5.2, 5.5.4, 5.7.1, 5.7.2_

    - [ ] 11.3 Implement `rejectSpam` private helper
        - Log the rejection reason and IP address
        - Return `response()->json(['message' => '...'], 422)` for AJAX, or `redirect()->back()->with('inquiry_error', '...')` for standard POST
        - Never expose internal rejection reason in the response body
        - _Requirements: 5.3.1, 5.3.2, 5.3.3, 5.6.3_

- [ ]   12. Create `HomeController` and `SitemapController`
    - [ ] 12.1 Create `app/Http/Controllers/HomeController.php`
        - Single `index` method returning `view('home')`
        - _Requirements: 10.1.1_

    - [ ] 12.2 Create `app/Http/Controllers/SitemapController.php`
        - Single `index` method returning XML response from a Blade view
        - Set `Content-Type: application/xml` header
        - _Requirements: 10.1.5_

- [ ]   13. Register rate limiter and routes
    - [ ] 13.1 Register `contact` named rate limiter in `bootstrap/app.php`
        - `RateLimiter::for('contact', ...)` - 5 requests per hour per IP
        - Custom 429 response: JSON for AJAX, redirect with flash for standard POST
        - _Requirements: 5.3.4_

    - [ ] 13.2 Define routes in `routes/web.php`
        - `GET /` → `HomeController@index` named `home`
        - `POST /contact` → `ContactController@store` with `throttle:contact`, named `contact.store`
        - `GET /sitemap.xml` → `SitemapController@index` named `sitemap`
        - _Requirements: 10.1.5_

---

## Phase 4 - Mail

- [ ]   14. Create `InquiryNotification` mailable
    - [ ] 14.1 Generate `app/Mail/InquiryNotification.php`
        - Run `php artisan make:mail InquiryNotification --markdown`
        - Implement `ShouldQueue`
        - _Requirements: 5.5.2_

    - [ ] 14.2 Configure `InquiryNotification` envelope and content
        - `From`: `config('mail.from.address')` / `config('mail.from.name')`
        - `Reply-To`: `$inquiry->email`
        - `To`: `config('clearford.company.email')` (resolves to `INQUIRY_TO_ADDRESS`)
        - `Subject`: `"New Project Inquiry from {$inquiry->name} – {$inquiry->company}"`
        - Content view: `emails.inquiry-notification`
        - _Requirements: 5.5.2, 5.5.3_

    - [ ] 14.3 Implement `failed(Throwable $e)` method on `InquiryNotification`
        - Log error with `mailable`, `inquiry_id`, and `error` message
        - _Requirements: 5.5.5_

- [ ]   15. Create `InquiryAutoReply` mailable
    - [ ] 15.1 Generate `app/Mail/InquiryAutoReply.php`
        - Run `php artisan make:mail InquiryAutoReply --markdown`
        - Implement `ShouldQueue`
        - _Requirements: 5.5.4_

    - [ ] 15.2 Configure `InquiryAutoReply` envelope and content
        - `From`: `config('mail.from.address')` / `config('mail.from.name')`
        - `To`: `$inquiry->email`
        - `Subject`: `"We received your inquiry – Clearford Global"`
        - Content view: `emails.inquiry-auto-reply`
        - Guard dispatch in controller behind `env('INQUIRY_AUTO_REPLY_ENABLED') === 'true'`
        - _Requirements: 5.5.4_

    - [ ] 15.3 Implement `failed(Throwable $e)` method on `InquiryAutoReply`
        - Log error with `mailable`, `inquiry_id`, and `error` message
        - _Requirements: 5.5.5_

- [ ]   16. Create email Blade templates
    - [ ] 16.1 Create `resources/views/emails/inquiry-notification.blade.php`
        - Display all inquiry fields in a plain, readable layout
        - Include: name, company, email, service, details, submission timestamp, IP address
        - _Requirements: 5.5.2_

    - [ ] 16.2 Create `resources/views/emails/inquiry-auto-reply.blade.php`
        - Warm, professional confirmation message
        - Do not make promises about response timelines (Requirements 16.1.4 applies to copy)
        - _Requirements: 5.5.4_

- [ ]   17. Update `config/mail.php`
    - [ ] 17.1 Add `inquiry_to` key referencing `INQUIRY_TO_ADDRESS` env variable
        - _Requirements: 5.5.2_

    - [ ] 17.2 Add `send_auto_reply` key referencing `INQUIRY_AUTO_REPLY_ENABLED` env variable
        - _Requirements: 5.5.4_

---

## Phase 5 - Blade Layout & Design System

- [ ]   18. Define Tailwind brand tokens in `resources/css/app.css`
    - [ ] 18.1 Add `@theme` directive with brand colour tokens
        - `--color-navy: #1b2a4a`, `--color-teal: #2aafa0`, `--color-white: #ffffff`
        - Add comment: `/* PENDING CLIENT CONFIRMATION */` next to navy and teal
        - `--font-sans: "Inter", ui-sans-serif, system-ui, sans-serif`
        - _Requirements: 7.1.1, 16.1.1_

    - [ ] 18.2 Add `.form-input` component class in `@layer components`
        - Apply: full width, rounded border, padding, focus ring using teal, disabled state styles
        - _Requirements: 5.1.1, 8.1.2_

    - [ ] 18.3 Add `@media (prefers-reduced-motion: reduce)` block
        - Set `animation-duration`, `animation-iteration-count`, `transition-duration`, `scroll-behavior` overrides
        - _Requirements: 8.1.6_

- [ ]   19. Create `<x-layout>` blade component
    - [ ] 19.1 Create `resources/views/components/layout.blade.php`
        - Accept `$title` and `$description` props with defaults from `config('clearford.seo')`
        - Render `<!DOCTYPE html>`, `<html lang="en">`, `<head>` with charset and viewport
        - _Requirements: 10.1.1_

    - [ ] 19.2 Add `<head>` meta and SEO tags to layout
        - `<title>`, `<meta name="description">`, CSRF meta tag
        - Open Graph tags: `og:title`, `og:description`, `og:image`, `og:url`, `og:type`
        - Twitter Card tags: `twitter:card`, `twitter:title`, `twitter:description`, `twitter:image`
        - _Requirements: 10.1.3, 10.1.4_

    - [ ] 19.3 Add Organization JSON-LD structured data to layout
        - Create `resources/views/partials/json-ld.blade.php` with Organization schema
        - Include `@context`, `@type`, `name`, `url`, `logo`, `contactPoint`
        - Include partial in layout `<head>`
        - _Requirements: 10.1.7_

    - [ ] 19.4 Add favicon link tags to layout `<head>`
        - `<link rel="icon" href="/favicon.ico" sizes="any">`
        - `<link rel="icon" href="/logo/Logowithoutbg.png" type="image/png" sizes="192x192">`
        - `<link rel="apple-touch-icon" href="/logo/Logowithoutbg.png">`
        - _Requirements: 10.1.8_

    - [ ] 19.5 Add skip-navigation link as first focusable element in layout
        - `<a href="#main-content" class="sr-only focus:not-sr-only ...">Skip to main content</a>`
        - Must appear before `<header>`
        - _Requirements: 1.3.2_

    - [ ] 19.6 Add Vite asset tags and reCAPTCHA script to layout
        - `@vite(['resources/css/app.css', 'resources/js/app.js'])`
        - reCAPTCHA script: `https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}` with `async defer`
        - Inline `<script>const RECAPTCHA_SITE_KEY = '...';</script>` before closing `</body>`
        - _Requirements: 5.3.3_

    - [ ] 19.7 Add sticky `<header>` with Alpine.js mobile nav component to layout
        - `x-data="mobileNav()"` on header element, `@keydown.escape.window="handleEscape($event)"`
        - Logo image (`public/logo/Logowithoutbg.png`), desktop nav links, "Discuss a Project" CTA
        - Hamburger button with `:aria-expanded="open"`, `:aria-label` toggle, `@click="toggle()"`
        - Mobile nav drawer with `x-show="open"`, `x-trap="open"`, nav links with `@click="close()"`
        - _Requirements: 1.1.1, 1.1.2, 1.1.3, 1.1.5, 1.2.1, 1.2.2, 1.2.3, 1.2.4, 1.2.5, 1.3.1_

    - [ ] 19.8 Add `<main id="main-content">` and `<footer>` to layout
        - `<main>` renders `{{ $slot }}`
        - Footer: logo, copyright with current year, nav links, placeholder email, Privacy/Terms placeholder links
        - _Requirements: 6.1.1, 6.1.2, 6.1.3, 6.1.4, 6.1.5_

- [ ]   20. Create button and section-header blade components
    - [ ] 20.1 Create `resources/views/components/btn-primary.blade.php`
        - Accept `$href` (optional) and `$type` (default `button`) props
        - Render `<a>` if `$href` is provided, otherwise `<button>`
        - Apply navy background, white text, focus ring, hover state
        - _Requirements: 8.1.2_

    - [ ] 20.2 Create `resources/views/components/btn-secondary.blade.php`
        - Accept `$href` (optional) and `$type` props
        - Render `<a>` or `<button>` based on `$href`
        - Apply teal border/text, transparent background, focus ring, hover state
        - _Requirements: 8.1.2_

    - [ ] 20.3 Create `resources/views/components/section-header.blade.php`
        - Accept `$title` and `$subtitle` (optional) props
        - Render `$title` as `<h2>`, `$subtitle` as `<p>`
        - Consistent section heading spacing
        - _Requirements: 10.1.2_

---

## Phase 6 - Page Sections

- [ ]   21. Create service and process step blade components
    - [ ] 21.1 Create `resources/views/components/service-card.blade.php`
        - Accept `$service` array prop with keys: `icon` (SVG string), `title`, `description`, `deliverables`
        - Render as `<article>` with inline SVG, `<h3>` title, description `<p>`, deliverables `<ul>`
        - SVG icon must have `aria-hidden="true"` (decorative); article title provides the accessible label
        - _Requirements: 3.2.1, 8.1.4_

    - [ ] 21.2 Create `resources/views/components/process-step.blade.php`
        - Accept `$step` array prop with keys: `number`, `title`, `copy`
        - Render large decorative step number, `<h3>` title, `<p>` copy
        - _Requirements: 4.1.2_

- [ ]   22. Build `resources/views/home.blade.php`
    - [ ] 22.1 Scaffold `home.blade.php` with `<x-layout>` wrapper and all section anchors
        - Sections in order: `#home`, `#services`, `#how-we-work`, `#contact`
        - Use semantic HTML5: `<section>`, `<header>` (in layout), `<main>`, `<footer>` (in layout), `<nav>`
        - _Requirements: 10.1.1_

    - [ ] 22.2 Build Hero section (`#home`)
        - `<h1>Build better systems. Run better operations.</h1>` - exactly one H1 on the page
        - 2–3 sentences of intro copy (draft placeholder, flagged for client review per 16.1.4)
        - Two CTAs: `<x-btn-primary>` linking to `#contact`, `<x-btn-secondary>` linking to `#services`
        - Responsive layout: stacks on mobile (320px+), side-by-side on wider viewports
        - _Requirements: 2.1.1, 2.1.2, 2.1.3, 2.1.4, 10.1.2_

    - [ ] 22.3 Build Services section (`#services`)
        - `<x-section-header title="Our Services" />`
        - Responsive grid: single column on mobile, multi-column on tablet+
        - `@foreach (config('clearford.services') as $service)` → `<x-service-card :service="$service" />`
        - _Requirements: 3.1.1, 3.1.2, 3.2.3_

    - [ ] 22.4 Build How We Work section (`#how-we-work`)
        - `<x-section-header title="How We Work" />`
        - `@foreach (config('clearford.process') as $step)` → `<x-process-step :step="$step" />`
        - Steps stack vertically on mobile in numbered order
        - _Requirements: 4.1.1, 4.1.3_

    - [ ] 22.5 Build Contact form section (`#contact`)
        - `<x-section-header title="Discuss a Project" />`
        - `<form x-data="contactForm()" @submit.prevent="submit()" action="/contact" method="POST">`
        - Fields: name, company, email (type=email), service (select with six options), details (textarea)
        - Each field has an associated `<label>` element
        - Hidden honeypot field: `<input type="text" name="website" class="hidden" tabindex="-1" autocomplete="off">`
        - Hidden timestamp field: `<input type="hidden" name="_form_rendered_at" :value="formRenderedAt">`
        - `@csrf` token
        - Submit button with `:disabled="loading"` and loading indicator
        - Success confirmation message with `x-show="success"`
        - General error message with `x-show="generalError"`
        - Per-field error display bound to `errors` object
        - Live region `aria-live="polite"` for screen reader announcements on success/error
        - _Requirements: 5.1.1, 5.2.3, 5.3.1, 5.3.2, 5.6.1, 5.6.2, 5.6.3, 5.7.2, 5.8.1, 8.1.3_

---

## Phase 7 - Alpine.js Contact Form Component

- [ ]   23. Write `mobileNav()` Alpine.js component
    - [ ] 23.1 Create `resources/js/components/mobile-nav.js`
        - Export `mobileNav()` returning: `open`, `toggle()`, `close()`, `handleEscape(e)`
        - _Requirements: 1.2.1, 1.2.2, 1.2.4_

- [ ]   24. Write `contactForm()` Alpine.js component
    - [ ] 24.1 Create `resources/js/components/contact-form.js`
        - Export `contactForm()` returning: `fields` object, `errors` object, `loading`, `success`, `generalError`, `formRenderedAt: Date.now()`
        - _Requirements: 5.6.1, 5.6.2, 5.6.3, 5.7.1_

    - [ ] 24.2 Implement `submit()` async method in `contactForm()`
        - Set `loading = true`, clear `errors` and `generalError`
        - Await `grecaptcha.execute(RECAPTCHA_SITE_KEY, { action: 'contact' })` to get token
        - Build payload: `{...this.fields, recaptcha_token: token, _form_rendered_at: this.formRenderedAt}`
        - `fetch('/contact', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': ..., 'Accept': 'application/json' }, body: JSON.stringify(payload) })`
        - On 200: set `this.success = true`
        - On 422: set `this.errors = data.errors`
        - On 429: set `this.generalError` to rate-limit message
        - On 5xx or network error: set `this.generalError` to generic message
        - Always set `loading = false` in `finally` block
        - Preserve field values on error (do not reset `this.fields` on failure)
        - Disable submit button while `loading` is true
        - _Requirements: 5.2.3, 5.6.1, 5.6.2, 5.6.3, 5.7.1, 5.8.1_

- [ ]   25. Register Alpine components in `resources/js/app.js`
    - [ ] 25.1 Import and register all Alpine components in `app.js`
        - Import `Alpine` from `alpinejs`
        - Import `focus` from `@alpinejs/focus`
        - Import `mobileNav` from `./components/mobile-nav.js`
        - Import `contactForm` from `./components/contact-form.js`
        - `Alpine.plugin(focus)`
        - `Alpine.data('mobileNav', mobileNav)`
        - `Alpine.data('contactForm', contactForm)`
        - `Alpine.start()`
        - _Requirements: 1.2.5_

---

## Phase 8 - SEO & Performance

- [ ]   26. SEO assets and sitemap
    - [ ] 26.1 Create `resources/views/sitemap.blade.php`
        - XML structure with single `<url>` entry for `/` with `<changefreq>monthly</changefreq>`
        - Use `{{ $url }}` for the base URL
        - _Requirements: 10.1.5_

    - [ ] 26.2 Verify `public/robots.txt` exists and is correct
        - Must have `User-agent: *`, `Allow: /`, `Sitemap:` pointing to `/sitemap.xml`
        - Add comment noting domain must be updated when production domain is confirmed
        - _Requirements: 10.1.6_

    - [ ] 26.3 Add `services.recaptcha` keys to `config/services.php`
        - `'site_key' => env('RECAPTCHA_SITE_KEY')`, `'secret' => env('RECAPTCHA_SECRET_KEY')`
        - _Requirements: 5.3.3_

- [ ]   27. Performance and image attributes
    - [ ] 27.1 Add `loading="lazy"` to any below-the-fold `<img>` elements in Blade templates
        - Hero logo/image (if any) should NOT be lazy-loaded (above the fold)
        - Footer logo should use `loading="lazy"`
        - _Requirements: 9.1.2_

    - [ ] 27.2 Confirm exactly one `<h1>` on the page
        - Review `home.blade.php` and `layout.blade.php` to ensure no `<h1>` in layout shell
        - _Requirements: 10.1.2_

    - [ ] 27.3 Confirm all `<img>` elements have descriptive `alt` text or `alt=""`
        - Logo images: `alt="Clearford Global logo"`
        - Decorative images: `alt=""`
        - _Requirements: 8.1.4_

---

## Phase 9 - Tests

- [ ]   28. Configure Pest test bootstrap
    - [ ] 28.1 Update `tests/Pest.php` to use `RefreshDatabase` for feature tests
        - `uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class)->in('Feature');`
        - _Requirements: 14.1.1_

    - [ ] 28.2 Create `database/factories/InquiryFactory.php`
        - Define factory with valid default values for all `$fillable` fields
        - _Requirements: 14.1.1_

- [ ]   29. Write `tests/Feature/ContactFormTest.php`
    - [ ] 29.1 Write `beforeEach` setup block
        - `Mail::fake()`, mock `RecaptchaService::verify()` to return `0.9` by default
        - Helper to build a valid submission payload
        - _Requirements: 14.1.1_

    - [ ] 29.2 Write happy-path test: valid AJAX submission
        - Assert `200` status, JSON `success: true`
        - Assert `assertDatabaseHas('inquiries', [...])` with correct field values
        - Assert `Mail::assertQueued(InquiryNotification::class)`
        - _Requirements: 5.5.1, 5.5.2, 5.6.2, 14.1.1_

    - [ ] 29.3 Write happy-path test: auto-reply dispatched when enabled
        - Set `INQUIRY_AUTO_REPLY_ENABLED=true` in test
        - Assert `Mail::assertQueued(InquiryAutoReply::class)`
        - _Requirements: 5.5.4_

    - [ ] 29.4 Write test: auto-reply NOT dispatched when disabled
        - Set `INQUIRY_AUTO_REPLY_ENABLED=false`
        - Assert `Mail::assertNotQueued(InquiryAutoReply::class)`
        - _Requirements: 5.5.4_

    - [ ] 29.5 Write validation failure tests for each required field
        - Missing `name`: assert `422`, `errors.name` present, `assertDatabaseMissing`
        - Missing `company`: assert `422`, `errors.company` present
        - Missing `email`: assert `422`, `errors.email` present
        - Invalid email format: assert `422`, `errors.email` present
        - Missing `service`: assert `422`, `errors.service` present
        - Invalid `service` value (not in allowed list): assert `422`, `errors.service` present
        - Missing `details`: assert `422`, `errors.details` present
        - `details` too short (< 20 chars): assert `422`, `errors.details` present
        - `details` too long (> 2000 chars): assert `422`, `errors.details` present
        - _Requirements: 5.2.1, 5.2.2, 14.1.2_

    - [ ] 29.6 Write honeypot test
        - Submit with `website` field populated
        - Assert submission rejected, no DB record created
        - Assert no mail queued
        - _Requirements: 5.3.1, 14.1.3_

    - [ ] 29.7 Write timestamp check test
        - Submit with `_form_rendered_at` set to `time() * 1000` (0 ms elapsed)
        - Assert submission rejected, no DB record created
        - _Requirements: 5.3.2, 14.1.4_

    - [ ] 29.8 Write reCAPTCHA score test
        - Mock `RecaptchaService::verify()` to return `0.3` (below threshold)
        - Assert submission rejected, no DB record created
        - _Requirements: 5.3.3_

    - [ ] 29.9 Write rate limit test
        - Submit 6 requests from same IP
        - Assert 6th request returns `429`
        - _Requirements: 5.3.4, 14.1.5_

    - [ ] 29.10 Write notification email envelope test
        - Assert `Mail::assertQueued(InquiryNotification::class, fn($m) => $m->hasTo(config('clearford.company.email')) && $m->hasReplyTo($inquiry->email))`
        - Assert subject contains inquiry name and company
        - _Requirements: 5.5.3, 14.1.6_

    - [ ] 29.11 Write no-JS success path test
        - Submit as standard form POST (without `Accept: application/json`)
        - Assert redirect, session flash `inquiry_success` is set
        - Assert DB record created
        - _Requirements: 5.7.2_

    - [ ] 29.12 Write no-JS validation failure test
        - Submit invalid form without `Accept: application/json`
        - Assert redirect, session `errors` populated, old input present
        - _Requirements: 5.2.2, 5.2.3, 5.7.2_

    - [ ] 29.13 Write duplicate submission test
        - Submit same email + details twice within deduplication window
        - Assert second submission rejected, only one DB record exists
        - _Requirements: 5.4.1_

- [ ]   30. Write `tests/Feature/HomepageTest.php`
    - [ ] 30.1 Write test: `GET /` returns 200
        - Assert response status 200
        - _Requirements: 9.1.1_

    - [ ] 30.2 Write test: homepage contains correct H1
        - Assert response contains `<h1>Build better systems. Run better operations.</h1>`
        - Assert exactly one `<h1>` element (use `assertDontSee` for a second h1 or count occurrences)
        - _Requirements: 2.1.1, 10.1.2_

    - [ ] 30.3 Write test: homepage contains all section anchors
        - Assert response contains `id="home"`, `id="services"`, `id="how-we-work"`, `id="contact"`
        - _Requirements: 1.1.3, 1.1.4_

    - [ ] 30.4 Write test: `GET /sitemap.xml` returns 200 with XML content-type
        - Assert status 200
        - Assert `Content-Type` header contains `application/xml`
        - _Requirements: 10.1.5_

    - [ ] 30.5 Write test: security headers are present on all responses
        - Assert `X-Frame-Options: DENY` on `GET /`
        - Assert `X-Content-Type-Options: nosniff` on `GET /`
        - Assert `Referrer-Policy: strict-origin-when-cross-origin` on `GET /`
        - _Requirements: 11.1.2_

---

## Phase 10 - QA & Documentation

- [ ]   31. Update `README.md`
    - [ ] 31.1 Write local development setup section
        - Prerequisites: PHP 8.2+, Composer, Node.js, npm
        - Steps: `git clone`, `composer install`, `npm install`, `cp .env.example .env`, `php artisan key:generate`, `php artisan migrate`
        - _Requirements: 15.1.3_

    - [ ] 31.2 Write asset build steps section
        - `npm run build` for production, `npm run dev` for development watch
        - _Requirements: 15.1.3_

    - [ ] 31.3 Write `.env` configuration section
        - Document each key group: app, database, mail (Hostinger SMTP settings), inquiry routing, reCAPTCHA, queue
        - Note which values are PENDING CLIENT CONFIRMATION
        - _Requirements: 15.1.2, 15.1.3_

    - [ ] 31.4 Write "how to change inbox address" section
        - Update `INQUIRY_TO_ADDRESS` in `.env`
        - _Requirements: 15.1.3_

    - [ ] 31.5 Write "how to edit service content" section
        - Explain editing `config/clearford.php` `services` array
        - Note that no Blade files need to be touched
        - _Requirements: 15.1.3_

    - [ ] 31.6 Write deployment to Hostinger section
        - Upload files, set `APP_ENV=production`, `APP_DEBUG=false`, run `php artisan migrate --force`, run `npm run build`, configure queue worker
        - _Requirements: 15.1.3_

    - [ ] 31.7 Write queue worker setup section
        - For shared hosting: `php artisan queue:work --once` via cron
        - For VPS: Supervisor config example
        - _Requirements: 15.1.3_

- [ ]   32. Create deployment checklist
    - [ ] 32.1 Create `DEPLOY.md` deployment checklist
        - Environment: `APP_DEBUG=false`, `APP_ENV=production`, `APP_KEY` set, `APP_URL` updated to production domain
        - Database: migration run, SQLite file writable by web server
        - Mail: SMTP credentials verified, `INQUIRY_TO_ADDRESS` confirmed with client
        - reCAPTCHA: site and secret keys confirmed with client and set in env
        - DNS/domain: `robots.txt` domain updated, JSON-LD `url` updated
        - Assets: `npm run build` output committed or deployed, `public/storage` symlinked if needed
        - Queue: worker configured and running
        - Security: verify security headers present in browser DevTools, verify HTTPS redirect, verify HSTS header in production
        - Accessibility: note requirement for manual testing with assistive technologies (Requirements 8.1.7, 14.1.7)
        - Privacy: confirm no analytics scripts loaded unless client approved (Requirements 12.1.1)
        - Open items: flag brand colours, font choice, Privacy Policy and Terms pages for client sign-off
        - _Requirements: 15.1.4_

- [ ]   33. Final verification
    - [ ] 33.1 Run full Pest test suite
        - Run `./vendor/bin/pest`
        - All tests must pass
        - _Requirements: 14.1.1_

    - [ ] 33.2 Run `npm run build` and verify no errors
        - Confirm no Vite/Tailwind/JS compilation errors
        - _Requirements: 9.1.3_

    - [ ] 33.3 Run `php artisan route:list` and verify all routes registered
        - Confirm `GET /`, `POST /contact`, `GET /sitemap.xml` are listed with correct middleware
        - _Requirements: 10.1.5_

    - [ ] 33.4 Manual smoke check in browser
        - Verify no console errors on page load
        - Verify sticky header, smooth scroll, and mobile hamburger menu work
        - Verify contact form submits and shows success state
        - Verify skip-navigation link is first focusable element
        - _Requirements: 1.1.1, 1.2.1, 5.6.2, 1.3.2_

## Notes

- Brand colours Navy (`#1B2A4A`) and Teal (`#2AAFA0`) are **PENDING CLIENT CONFIRMATION** (Requirements 16.1.1). Tokens are centralised in `resources/css/app.css` so a single change propagates everywhere.
- Google reCAPTCHA v3 integration is **PENDING CLIENT CONFIRMATION** (Requirements 16.1.2). `RecaptchaService` gracefully bypasses the check when keys are not set, so the site can be developed and tested locally without keys.
- `INQUIRY_TO_ADDRESS` is a **PLACEHOLDER** pending client confirmation (Requirements 16.1.3).
- Hero section intro copy is a **DRAFT** requiring client review and approval before launch (Requirements 16.1.4).
- Privacy Policy and Terms of Service pages are **NOT** in scope for launch; footer placeholder links are included as a reminder (Requirements 16.1.5).
- No third-party analytics scripts are installed at launch; this is **PENDING CLIENT CONFIRMATION** (Requirements 16.1.6).
- Minimum PHP runtime version is **8.2** (Requirements 16.1.7).
- Full WCAG 2.1 AA compliance requires manual testing with assistive technologies beyond automated checks (Requirements 8.1.7).
- Cross-browser testing (Chrome, Safari, Firefox, Edge, iOS Safari, Android Chrome) is a manual step listed in Requirements 14.1.7 and covered in the deployment checklist (task 32.1).
