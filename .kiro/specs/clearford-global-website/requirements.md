# Requirements Document

## Introduction

Clearford is a professional services company offering software development, data analytics, QA, business analysis, and healthcare technology services. This feature delivers a greenfield single-page marketing website built on Laravel 12, Tailwind CSS v4, and Blade templates.

The site serves as Clearford's primary web presence: introducing the company, presenting its services, explaining how it works, and providing a secure project inquiry form. The goal is a clean, corporate, mobile-first site that generates qualified leads while maintaining high standards of accessibility, performance, and security.

## Requirements

### 1. Navigation and Header

#### 1.1 Sticky Header

1.1.1 WHEN any page section is scrolled THEN the header SHALL remain fixed at the top of the viewport at all times.

1.1.2 WHEN the page loads THEN the header SHALL display the Clearford logo (public/logo/Logowithoutbg.png) on the left side.

1.1.3 WHEN the page loads on a desktop viewport THEN the header SHALL display inline navigation links: Home, Services, How We Work, and Contact.

1.1.4 WHEN a navigation link is clicked THEN the page SHALL smooth-scroll to the corresponding anchor section.

1.1.5 WHEN the page loads THEN the header SHALL display a "Discuss a Project" call-to-action button that scrolls to the Contact section.

#### 1.2 Mobile Navigation

1.2.1 WHEN the viewport width is below the mobile breakpoint THEN the header SHALL hide the inline navigation links and show a hamburger menu button instead.

1.2.2 WHEN the hamburger button is clicked THEN the navigation menu SHALL expand to show all nav links and the CTA button.

1.2.3 WHEN an expanded menu link is clicked THEN the mobile menu SHALL close and the page SHALL smooth-scroll to the target section.

1.2.4 WHEN the mobile menu is open THEN pressing the Escape key SHALL close the menu.

1.2.5 WHEN the mobile menu is rendered THEN all interactive elements SHALL be reachable and operable via keyboard navigation.

#### 1.3 Accessibility - Header

1.3.1 WHEN the hamburger button is rendered THEN it SHALL have a descriptive `aria-label` and SHALL toggle `aria-expanded` state when opened or closed.

1.3.2 WHEN the site is loaded THEN a visible skip-navigation link SHALL be the first focusable element, linking to the main content area.

---

### 2. Hero Section (Home)

2.1.1 WHEN the Hero section is rendered THEN it SHALL display the H1 heading: "Build better systems. Run better operations."

2.1.2 WHEN the Hero section is rendered THEN it SHALL display 2–3 sentences of company introduction copy below the H1. The copy SHALL be drafted for client review and SHALL NOT invent statistics, specific client names, or unconfirmed claims.

2.1.3 WHEN the Hero section is rendered THEN it SHALL display two CTAs side by side:

- A primary "Discuss a Project" button linking to the Contact section (#contact).
- A secondary "Explore Services" button linking to the Services section (#services).

    2.1.4 WHEN the Hero section is rendered on a mobile viewport (320px and above) THEN the layout SHALL stack legibly without horizontal overflow.

---

### 3. Services Section

#### 3.1 Layout

3.1.1 WHEN the Services section is rendered THEN it SHALL display exactly five service cards arranged in a responsive grid.

3.1.2 WHEN the grid is rendered on mobile THEN cards SHALL stack in a single column. WHEN rendered on tablet or wider THEN cards SHALL display in a multi-column grid.

#### 3.2 Service Cards

3.2.1 WHEN a service card is rendered THEN it SHALL display: an inline SVG icon, a service title, a short description, and a short list of example deliverables or activities.

3.2.2 WHEN the Services section is rendered THEN it SHALL contain the following five services, with the content below treated as the initial draft for client review:

- **Software Development** - Web applications, internal tools, APIs, and integrations.
- **Data Analytics & Reporting** - Dashboards, SQL reporting, and data preparation.
- **Software QA & Testing** - Manual testing, API testing, regression testing, and test automation.
- **Business Analysis & Operations** - Requirements gathering, process mapping, workflow improvement, and implementation support.
- **Healthcare Technology & Operations** - Reporting, software testing, and administrative workflow support.

    3.2.3 WHEN the Services section content is managed THEN service titles, descriptions, and bullet lists SHALL be driven by a configuration array (not hardcoded in view templates) so content can be updated without touching Blade files.

    3.2.4 WHEN the Services section is rendered THEN no compliance certifications (e.g., HIPAA) SHALL be claimed unless confirmed by the client.

---

### 4. How We Work Section

4.1.1 WHEN the How We Work section is rendered THEN it SHALL display exactly three numbered process steps in order:

1. Agree on scope and deliverables.
2. Review progress at milestones.
3. Hand over completed work with documentation.

4.1.2 WHEN each step is rendered THEN it SHALL display a step number, a short step title, and one to two sentences of supporting copy.

4.1.3 WHEN the section is rendered on mobile THEN the steps SHALL stack vertically in numbered order.

---

### 5. Contact / Project Inquiry Form

#### 5.1 Form Fields

5.1.1 WHEN the Contact form is rendered THEN it SHALL contain the following fields:

- Name (text input, required).
- Company (text input, required).
- Work Email (email input, required, must be a valid email format).
- Service (select dropdown, required). Options: Software Development, Data Analytics & Reporting, Software QA & Testing, Business Analysis & Operations, Healthcare Technology & Operations, Not sure yet.
- Project Details (textarea, required).

    5.1.2 WHEN Project Details is validated THEN it SHALL enforce a minimum length of 20 characters and a maximum length of 2000 characters.

#### 5.2 Server-Side Validation

5.2.1 WHEN the form is submitted THEN all required fields SHALL be validated server-side via a Laravel Form Request class.

5.2.2 WHEN validation fails THEN the response SHALL include field-level error messages for each failing field.

5.2.3 WHEN validation fails THEN all previously entered field values SHALL be preserved and repopulated in the form.

#### 5.3 Spam Protection

5.3.1 WHEN the form is rendered THEN it SHALL include a hidden honeypot field. WHEN that field contains any value on submission THEN the submission SHALL be silently rejected.

5.3.2 WHEN the form is submitted THEN the server SHALL check that a minimum elapsed time (configurable, default 3 seconds) has passed since the form was rendered. WHEN the submission arrives too quickly THEN it SHALL be rejected.

5.3.3 WHEN the form is submitted THEN Google reCAPTCHA v3 SHALL be evaluated server-side using keys stored in environment variables. WHEN the reCAPTCHA score falls below the configured threshold THEN the submission SHALL be rejected.

5.3.4 WHEN multiple submissions arrive from the same IP address within a configurable window THEN the server SHALL enforce IP-based rate limiting and return a 429 response.

#### 5.4 Duplicate Prevention

5.4.1 WHEN a submission with the same Work Email and identical Project Details has already been recorded within a configurable deduplication window THEN the system SHALL reject the duplicate and return a user-friendly message rather than creating a second record.

#### 5.5 Submission Handling

5.5.1 WHEN a valid form is submitted THEN the system SHALL save the inquiry record to the database.

5.5.2 WHEN a valid form is submitted THEN the system SHALL dispatch a queued mail job to send a notification email to the address defined by the `INQUIRY_TO_ADDRESS` environment variable.

5.5.3 WHEN the notification email is sent THEN the Reply-To header SHALL be set to the submitter's Work Email address. The From address SHALL NEVER be set to the submitter's email address.

5.5.4 WHEN a valid form is submitted THEN an optional auto-reply email MAY be sent to the submitter. This behavior SHALL be configurable via an environment variable.

5.5.5 WHEN a queued mail job fails THEN the failure SHALL be logged to Laravel's log channel for operator review.

#### 5.6 Form UX States

5.6.1 WHEN the form is in the process of being submitted THEN the submit button SHALL be disabled and SHALL display a loading indicator to prevent double submission.

5.6.2 WHEN a submission succeeds THEN the form SHALL be replaced with or supplemented by a visible success confirmation message.

5.6.3 WHEN a general server error occurs (non-validation) THEN the form SHALL display a user-visible general error message without exposing internal details.

#### 5.7 AJAX Submission and Non-JS Fallback

5.7.1 WHEN JavaScript is available THEN the form SHALL submit via `fetch` / AJAX without a full page reload.

5.7.2 WHEN JavaScript is not available THEN the form SHALL fall back to a standard HTML form submission with server-rendered responses.

#### 5.8 Security - Form

5.8.1 WHEN the form is rendered THEN it SHALL include a Laravel CSRF token.

5.8.2 WHEN form input is stored or displayed THEN all user-supplied values SHALL be sanitised and escaped to prevent XSS.

5.8.3 WHEN email headers are constructed THEN all user-supplied values used in headers SHALL be sanitised to prevent header injection.

---

### 6. Footer

6.1.1 WHEN the footer is rendered THEN it SHALL display the Clearford logo.

6.1.2 WHEN the footer is rendered THEN it SHALL display a copyright line with the current year.

6.1.3 WHEN the footer is rendered THEN it SHALL display the same navigation links as the header (Home, Services, How We Work, Contact).

6.1.4 WHEN the footer is rendered THEN it SHALL display a contact email address. The specific address is a placeholder pending client confirmation.

6.1.5 WHEN the footer is rendered THEN it SHALL display placeholder links for Privacy Policy and Terms of Service. These pages do not need to exist at launch but are flagged for client review before go-live.

---

### 7. Visual Design and Brand

7.1.1 WHEN the site is rendered THEN it SHALL use the following brand colour palette (pending client confirmation):

- Navy: #1B2A4A
- Teal: #2AAFA0
- White: #FFFFFF

    7.1.2 WHEN the site is rendered THEN it SHALL use a single sans-serif typeface throughout, achieving a clean, professional, and corporate appearance.

    7.1.3 WHEN the site is rendered on any viewport from 320px wide to a wide desktop THEN all layouts SHALL be fully responsive with no horizontal overflow and no broken visual elements.

    7.1.4 WHEN the site is rendered THEN the design SHALL be mobile-first: base styles target small screens and are enhanced with breakpoint utilities for wider viewports.

---

### 8. Accessibility

8.1.1 WHEN the site is rendered THEN all colour combinations SHALL meet WCAG 2.1 AA minimum contrast ratios (4.5:1 for normal text, 3:1 for large text and UI components).

8.1.2 WHEN interactive elements (links, buttons, inputs) receive keyboard focus THEN a clearly visible focus indicator SHALL be displayed.

8.1.3 WHEN form fields are rendered THEN each field SHALL have an associated `<label>` element or an `aria-label` / `aria-labelledby` attribute.

8.1.4 WHEN icons or images are rendered THEN they SHALL have descriptive `alt` text or `aria-label` where appropriate. Decorative images SHALL have `alt=""`.

8.1.5 WHEN ARIA roles, states, and properties are used THEN they SHALL be valid and correctly applied per WAI-ARIA 1.1 specifications.

8.1.6 WHEN a user has enabled the `prefers-reduced-motion` media query THEN all CSS transitions and animations SHALL be suppressed or minimised.

8.1.7 WHEN the site is audited THEN full WCAG 2.1 AA compliance requires manual testing with assistive technologies and expert review; automated tooling alone is not sufficient for certification.

---

### 9. Performance

9.1.1 WHEN the site is audited with Google Lighthouse on a mobile device THEN it SHALL score 90 or above on Performance, Accessibility, Best Practices, and SEO.

9.1.2 WHEN raster images are served THEN they SHALL be provided in WebP format and SHALL use the `loading="lazy"` attribute on below-the-fold images.

9.1.3 WHEN the page is built THEN JavaScript bundle size SHALL be minimised; heavy third-party libraries SHALL be avoided unless strictly necessary.

---

### 10. SEO

10.1.1 WHEN the page is rendered THEN it SHALL use semantic HTML5 elements (`<header>`, `<main>`, `<section>`, `<footer>`, `<nav>`, etc.).

10.1.2 WHEN the page is rendered THEN there SHALL be exactly one `<h1>` element on the page.

10.1.3 WHEN the page is rendered THEN the `<head>` SHALL contain a unique `<title>` tag and a `<meta name="description">` tag.

10.1.4 WHEN the page is rendered THEN the `<head>` SHALL contain Open Graph meta tags (`og:title`, `og:description`, `og:image`, `og:url`) and Twitter Card meta tags.

10.1.5 WHEN the site is deployed THEN a `sitemap.xml` SHALL be accessible at `/sitemap.xml`.

10.1.6 WHEN the site is deployed THEN a `robots.txt` SHALL be accessible at `/robots.txt`.

10.1.7 WHEN the page is rendered THEN the `<head>` SHALL include an Organization JSON-LD structured data block describing the company.

10.1.8 WHEN the site is deployed THEN a favicon set (at minimum `favicon.ico` and a 192×192 PNG) SHALL be served.

---

### 11. Security

11.1.1 WHEN any form is submitted THEN Laravel's CSRF middleware SHALL validate the token.

11.1.2 WHEN the application handles HTTP responses THEN the following security headers SHALL be present: `Content-Security-Policy`, `Strict-Transport-Security`, `X-Frame-Options: DENY`, and `Referrer-Policy: strict-origin-when-cross-origin`.

11.1.3 WHEN the application is deployed to production THEN `APP_DEBUG` SHALL be set to `false` and no stack traces or internal error details SHALL be exposed to end users.

11.1.4 WHEN secrets (API keys, SMTP credentials, reCAPTCHA keys, database credentials) are managed THEN they SHALL be stored exclusively in environment variables and SHALL never be committed to the repository.

---

### 12. Privacy

12.1.1 WHEN the site is live THEN no third-party analytics or tracking scripts SHALL be loaded unless the client has explicitly approved them.

12.1.2 WHEN inquiry data is stored THEN it SHALL be used only for responding to the inquiry and SHALL not be shared with third parties.

---

### 13. Code Quality and Maintainability

13.1.1 WHEN PHP code is written THEN it SHALL conform to the PSR-12 coding standard.

13.1.2 WHEN UI elements that repeat across sections are built THEN they SHALL be implemented as Blade components to avoid duplication.

13.1.3 WHEN service content (titles, descriptions, bullet lists) is managed THEN it SHALL be stored in a configuration file or service array so that content editors can update copy without modifying Blade templates.

---

### 14. Testing

14.1.1 WHEN a valid inquiry form is submitted in tests THEN a Pest feature test SHALL assert that the submission is saved to the database, the notification email is dispatched, and a success response is returned.

14.1.2 WHEN each server-side validation rule is exercised in tests THEN a dedicated Pest test SHALL assert that the correct field-level error is returned and no record is saved.

14.1.3 WHEN the honeypot field is populated in a test submission THEN the test SHALL assert the submission is rejected without saving a record.

14.1.4 WHEN a submission arrives faster than the minimum elapsed-time threshold in a test THEN the test SHALL assert the submission is rejected.

14.1.5 WHEN the IP rate limit is exceeded in tests THEN the test SHALL assert a 429 response is returned.

14.1.6 WHEN a notification email is dispatched THEN the test SHALL assert the correct To address, Reply-To address (submitter's email), and subject line.

14.1.7 WHEN the site is manually tested THEN it SHALL be verified in the latest stable versions of Chrome, Safari, Firefox, and Edge on desktop, and on iOS Safari and Android Chrome.

---

### 15. Deliverables

15.1.1 WHEN the project is complete THEN the deliverable SHALL include the full Laravel application with all source code committed to the repository.

15.1.2 WHEN the project is complete THEN a `.env.example` file SHALL be present in the repository root documenting all required environment variables with placeholder values and inline comments.

15.1.3 WHEN the project is complete THEN a `README.md` SHALL be present documenting: local development setup, asset build steps, deployment procedure, and instructions for editing site content.

15.1.4 WHEN the project is complete THEN a deployment checklist document SHALL be provided covering environment configuration, production verification steps, and go-live checks.

---

### 16. Assumptions and Open Items

16.1.1 The brand colours Navy (#1B2A4A) and Teal (#2AAFA0) have been extracted from the logo and are PENDING CLIENT CONFIRMATION before finalisation.

16.1.2 Google reCAPTCHA v3 is the selected spam protection mechanism PENDING CLIENT CONFIRMATION. If reCAPTCHA is not approved, an alternative (e.g., hCaptcha or a purely server-side approach) must be agreed before implementation.

16.1.3 The SMTP mail provider is Hostinger. The destination inbox address (`INQUIRY_TO_ADDRESS`) is a PLACEHOLDER pending client confirmation.

16.1.4 The company introduction copy in the Hero section is a DRAFT for client review and must be approved before launch.

16.1.5 No Privacy Policy page or Terms of Service page will be built at launch. Footer placeholder links are included as a reminder that these pages are FLAGGED FOR CLIENT REVIEW before go-live.

16.1.6 No third-party analytics will be installed at launch. This assumption is PENDING CLIENT CONFIRMATION.

16.1.7 PHP 8.2 is the minimum runtime version.

---

## Glossary

| Term                      | Definition                                                                                                                                                                          |
| ------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| **Anchor nav**            | Single-page navigation that uses URL fragment identifiers (e.g., `#contact`) and smooth scrolling to move between sections on the same page.                                        |
| **Blade**                 | Laravel's server-side templating engine used to render HTML views.                                                                                                                  |
| **Blade component**       | A reusable, self-contained Blade template element (e.g., a card or button) that can be referenced across multiple views.                                                            |
| **Config-driven content** | Content (text, labels, lists) stored in a PHP configuration array or file rather than hardcoded into Blade templates, enabling updates without touching view code.                  |
| **CSP**                   | Content Security Policy - an HTTP response header that restricts the sources from which the browser may load resources, reducing XSS exposure.                                      |
| **CSRF**                  | Cross-Site Request Forgery - an attack where a malicious site causes a user's browser to perform unwanted actions on another site. Laravel mitigates this with a per-session token. |
| **Duplicate prevention**  | A server-side check that rejects a second form submission with the same email address and project details within a defined time window.                                             |
| **Header injection**      | An attack that inserts newline characters into email header values to manipulate recipients or other headers.                                                                       |
| **Honeypot field**        | A hidden form field that legitimate users never fill in; automated bots that complete all fields will populate it, allowing the server to reject the submission silently.           |
| **HSTS**                  | HTTP Strict Transport Security - a header that instructs browsers to only connect via HTTPS for a specified duration.                                                               |
| **Inquiry**               | A project enquiry submitted through the Contact form, stored in the database and forwarded by email to the Clearford team.                                                          |
| **JSON-LD**               | A method of encoding Linked Data using JSON, used here to embed Organization structured data for search engines.                                                                    |
| **Open Graph**            | A protocol (originally from Facebook) using `<meta>` tags to control how a URL is presented when shared on social platforms.                                                        |
| **Pest**                  | A PHP testing framework with an expressive, fluent syntax, used here for feature and unit tests.                                                                                    |
| **reCAPTCHA v3**          | Google's bot-detection service that scores interactions invisibly (0.0–1.0) without user challenges.                                                                                |
| **Reply-To**              | An email header specifying the address a recipient's mail client should use when replying, distinct from the From address.                                                          |
| **SQLite**                | A file-based relational database used in this project to persist inquiry records.                                                                                                   |
| **Tailwind CSS v4**       | A utility-first CSS framework integrated via the `@tailwindcss/vite` plugin.                                                                                                        |
| **Vite**                  | A fast front-end build tool used to compile and bundle CSS and JavaScript assets.                                                                                                   |
| **WCAG 2.1 AA**           | Web Content Accessibility Guidelines version 2.1, Level AA - an internationally recognised standard for web accessibility.                                                          |
| **WebP**                  | A modern image format offering superior compression compared to JPEG/PNG, used to improve page load performance.                                                                    |
| **XSS**                   | Cross-Site Scripting - an attack where malicious scripts are injected into web pages viewed by other users.                                                                         |
