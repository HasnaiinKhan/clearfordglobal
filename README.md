# Clearford Global - Marketing Website

Single-page marketing website for Clearford Global, built with Laravel 12, Tailwind CSS v4, Alpine.js, and Vite 7.

---

## Stack

| Layer      | Technology                                |
| ---------- | ----------------------------------------- |
| Framework  | Laravel 12                                |
| PHP        | 8.2+                                      |
| CSS        | Tailwind CSS v4 (via `@tailwindcss/vite`) |
| JS         | Alpine.js v3, minimal vanilla JS          |
| Build tool | Vite 7                                    |
| Database   | SQLite (inquiry backup)                   |
| Mail       | SMTP via Hostinger                        |
| Queue      | Database queue driver                     |
| Tests      | Pest 3                                    |

---

## Local Setup

### Prerequisites

- PHP 8.2+
- Composer
- Node.js 20+
- SQLite (bundled with XAMPP)

### Steps

```bash
# 1. Clone and install PHP dependencies
composer install

# 2. Copy environment file
cp .env.example .env

# 3. Generate application key
php artisan key:generate

# 4. Run migrations (creates the inquiries table)
php artisan migrate

# 5. Install Node dependencies
npm install

# 6. Build frontend assets
npm run build
```

### Development

To run the full dev stack (Laravel server + queue worker + Vite HMR):

```bash
composer dev
```

This runs concurrently:

- `php artisan serve` - Laravel dev server on http://localhost:8000
- `php artisan queue:listen` - Processes queued mail jobs
- `npm run dev` - Vite HMR for CSS/JS

---

## Environment Variables

Copy `.env.example` to `.env` and fill in:

| Variable                | Description                                               |
| ----------------------- | --------------------------------------------------------- |
| `APP_URL`               | Full URL of the site (e.g. `https://clearfordglobal.com`) |
| `APP_DEBUG`             | Set to `false` in production                              |
| `MAIL_MAILER`           | `smtp` for production, `log` for development              |
| `MAIL_HOST`             | Hostinger SMTP host: `smtp.hostinger.com`                 |
| `MAIL_PORT`             | `465` (SSL) or `587` (TLS)                                |
| `MAIL_USERNAME`         | Your Hostinger email address                              |
| `MAIL_PASSWORD`         | Your Hostinger email password                             |
| `MAIL_FROM_ADDRESS`     | The From address (must be a verified Hostinger mailbox)   |
| `MAIL_FROM_NAME`        | Sender name shown in email clients                        |
| `INQUIRY_TO_ADDRESS`    | **Where project inquiries are delivered**                 |
| `MAIL_SEND_AUTO_REPLY`  | `true` to send confirmation emails to form submitters     |
| `RECAPTCHA_SITE_KEY`    | Google reCAPTCHA v3 site key                              |
| `RECAPTCHA_SECRET_KEY`  | Google reCAPTCHA v3 secret key                            |
| `COMPANY_CONTACT_EMAIL` | Email shown in the footer and contact section             |

### Hostinger SMTP Configuration

```env
MAIL_MAILER=smtp
MAIL_SCHEME=ssl
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=your-email@yourdomain.com
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=your-email@yourdomain.com
MAIL_FROM_NAME="Clearford Global"
```

### Google reCAPTCHA v3

1. Go to https://www.google.com/recaptcha/admin/create
2. Choose **reCAPTCHA v3**
3. Add your domain(s)
4. Copy the **Site Key** → `RECAPTCHA_SITE_KEY`
5. Copy the **Secret Key** → `RECAPTCHA_SECRET_KEY`

Leave both empty to disable reCAPTCHA (not recommended for production).

---

## Changing the Inquiry Destination Inbox

Edit `INQUIRY_TO_ADDRESS` in your `.env` file:

```env
INQUIRY_TO_ADDRESS=your-new-inbox@example.com
```

Then run `php artisan config:clear` on the server.

---

## Editing Site Content

All editable content (services, company intro, process steps, SEO tags, nav links) lives in one file:

```
config/clearford.php
```

Changes take effect immediately in development. On production, run:

```bash
php artisan config:cache
```

### Adding or Editing a Service

Each service in `config/clearford.php` under `services` has:

```php
[
    'key'         => 'unique-slug',           // matches form dropdown value
    'title'       => 'Service Name',
    'description' => 'Short description.',
    'bullets'     => ['Point 1', 'Point 2'],
    'icon'        => 'code',                   // see service-card.blade.php for options
],
```

If you add a service, also add its `key` to the `in:` rule in `ContactController::validationRules()`.

---

## Build Steps

### Development

```bash
npm run dev
```

### Production

```bash
npm run build
```

Built assets are written to `public/build/`. The `public/build/manifest.json` is used by the `@vite()` Blade directive.

---

## Deployment Steps

### Initial Deployment

1. Upload all project files to the server (excluding `vendor/`, `node_modules/`, `.env`)
2. SSH into the server and run:

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
# Edit .env with production values
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. Build assets locally and upload `public/build/`:

```bash
npm ci
npm run build
```

4. Set up a queue worker (see below)
5. Configure web server document root to `public/`

### Queue Worker

Mail jobs are queued and require a worker. For production on shared hosting (Hostinger), set up a cron job:

```
* * * * * cd /path/to/clearford && php artisan schedule:run >> /dev/null 2>&1
```

Add this to `routes/console.php` to process queue via scheduler if a persistent worker is not available:

```php
Schedule::command('queue:work --max-jobs=10 --stop-when-empty')->everyMinute();
```

For VPS with Supervisor, create `/etc/supervisor/conf.d/clearford-worker.conf`:

```ini
[program:clearford-worker]
command=php /path/to/clearford/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
stderr_logfile=/var/log/clearford-worker.log
```

### Post-Deployment Checklist

- [ ] `APP_DEBUG=false` in `.env`
- [ ] `APP_ENV=production` in `.env`
- [ ] SSL certificate installed and HTTPS working
- [ ] Send a test inquiry and confirm delivery to `INQUIRY_TO_ADDRESS`
- [ ] Check spam folder for test email
- [ ] Verify auto-reply arrives in submitter inbox
- [ ] SPF record includes Hostinger's servers
- [ ] DKIM record added to DNS
- [ ] DMARC record added to DNS
- [ ] Check Google Search Console after submitting sitemap
- [ ] Run Lighthouse audit (target 90+ on all categories)

---

## Post-Launch Email Delivery Test Record

| Test                            | Date | Result | Notes             |
| ------------------------------- | ---- | ------ | ----------------- |
| Test inquiry delivered to inbox | -    | -      | -                 |
| Test inquiry not in spam        | -    | -      | -                 |
| Auto-reply delivered            | -    | -      | -                 |
| SPF pass                        | -    | -      | Use mxtoolbox.com |
| DKIM pass                       | -    | -      | Use mxtoolbox.com |
| DMARC pass                      | -    | -      | Use mxtoolbox.com |

---

## Running Tests

```bash
# Run all tests
php vendor/bin/pest

# Run only contact form tests
php vendor/bin/pest tests/Feature/ContactFormTest.php

# Run with coverage (requires Xdebug or PCOV)
php vendor/bin/pest --coverage
```

---

## Cross-Browser Manual Testing Checklist

- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] iOS Safari (iPhone)
- [ ] Chrome Android

For each browser, verify:

- [ ] Navigation and smooth scroll work
- [ ] Mobile hamburger menu opens/closes
- [ ] Form submits successfully
- [ ] Form shows field errors correctly
- [ ] Form shows success message after submit

---

## Privacy Policy and Terms of Service

Both pages are flagged as "coming soon" in the footer. When ready:

1. Create Blade views at `resources/views/privacy.blade.php` and `resources/views/terms.blade.php`
2. Add routes in `routes/web.php`
3. Set in `config/clearford.php`:

```php
'show_privacy' => true,
'show_terms'   => true,
```

---

## Security Notes

- CSRF protection is active on all forms
- Security headers (X-Frame-Options, CSP, Referrer-Policy, etc.) are set via `SecurityHeaders` middleware
- HSTS is only applied in `production` environment
- reCAPTCHA v3 + honeypot + time-based check on the contact form
- IP rate limiting: 5 submissions per hour per IP
- Never commit `.env` to version control
- The `From` address is always your own domain mailbox - the sender's email is set as `Reply-To` only

---

## Analytics

No tracking or analytics scripts are included. Add Google Analytics or Plausible only after updating the Privacy Policy.
