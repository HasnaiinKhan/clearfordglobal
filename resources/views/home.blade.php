<x-layout>

{{-- ══ STICKY HEADER ══════════════════════════════════════════════════════ --}}
<header class="site-header" role="banner" x-data="mobileNav()" @keydown.escape.window="handleEscape($event)">
    <div class="container">
        <div class="header-inner">

            {{-- Logo --}}
            <a href="#home" class="header-logo" aria-label="{{ config('clearford.company.name') }} - go to top">
                <img src="/logo/Logowithoutbg.png" alt="{{ config('clearford.company.name') }}" width="192" height="48">
            </a>

            {{-- Desktop Nav --}}
            <nav class="header-nav" aria-label="Main navigation">
                @foreach(config('clearford.nav') as $item)
                <a href="{{ $item['href'] }}">{{ $item['label'] }}</a>
                @endforeach
            </nav>

            {{-- Desktop CTA --}}
            <div class="header-cta">
                <a href="#contact" class="btn btn-navy btn-sm">Discuss a Project</a>
            </div>

            {{-- Mobile hamburger --}}
            <button
                class="hamburger"
                type="button"
                @click="toggle()"
                :aria-expanded="open.toString()"
                aria-controls="mobile-menu"
                aria-label="Toggle navigation menu"
            >
                <span class="sr-only" x-text="open ? 'Close menu' : 'Open menu'"></span>
                <svg x-show="!open" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
                <svg x-show="open" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <nav
        id="mobile-menu"
        class="mobile-menu"
        x-show="open"
        x-trap="open"
        x-transition:enter="transition-nav-enter"
        x-transition:leave="transition-nav-leave"
        aria-label="Mobile navigation"
    >
        @foreach(config('clearford.nav') as $item)
        <a href="{{ $item['href'] }}" @click="close()">{{ $item['label'] }}</a>
        @endforeach
        <div class="mobile-menu-cta">
            <a href="#contact" class="btn btn-navy btn-sm btn-block" @click="close()">Discuss a Project</a>
        </div>
    </nav>
</header>

<main id="main-content">

{{-- ══ HERO ════════════════════════════════════════════════════════════════ --}}
<section id="home" class="hero" aria-labelledby="hero-heading">
    <div class="hero-bg-gradient" aria-hidden="true"></div>
    <div class="hero-bg-accent"   aria-hidden="true"></div>
    <div class="container">
        <div class="hero-layout">

            {{-- Left: text content --}}
            <div class="hero-content">
                <p class="hero-label hero-animate">Clearford Global</p>
                <h1 id="hero-heading" class="hero-heading hero-animate" style="animation-delay:.15s">
                    {{ config('clearford.company.tagline') }}
                </h1>
                <p class="hero-intro hero-animate" style="animation-delay:.3s">
                    {{ config('clearford.company.intro') }}
                </p>
                <p class="hero-detail hero-animate" style="animation-delay:.45s">
                    {{ config('clearford.company.intro_detail') }}
                </p>
                <div class="hero-actions hero-animate" style="animation-delay:.6s">
                    <a href="#contact"  class="btn btn-primary">Discuss a Project</a>
                    <a href="#services" class="btn btn-secondary">Explore Services</a>
                </div>
            </div>

            {{-- Right: illustration --}}
            <div class="hero-image hero-animate" style="animation-delay:.35s" aria-hidden="true">
                <img
                    src="/images/top.png"
                    alt="Software development illustration"
                    width="886"
                    height="589"
                    class="hero-img"
                >
            </div>

        </div>
    </div>
</section>

{{-- ══ SERVICES ════════════════════════════════════════════════════════════ --}}
<section id="services" class="section-pad section-bg-light" aria-labelledby="services-heading">
    <div class="container">

        <div class="services-header animate-on-scroll">
            <h2 id="services-heading" class="section-heading">What we do</h2>
            <p class="section-subheading">
                We focus on delivery - structured work, documented outcomes, and clean handoffs across five core service areas.
            </p>
        </div>

        <div class="services-grid stagger">
            @foreach(config('clearford.services') as $service)
                <x-service-card :service="$service" />
            @endforeach
        </div>

    </div>
</section>

{{-- ══ HOW WE WORK ═════════════════════════════════════════════════════════ --}}
<section id="process" class="section-pad section-bg-white" aria-labelledby="process-heading">
    <div class="container">
        <div class="process-layout">

            {{-- Left: sticky heading --}}
            <div class="process-sticky animate-on-scroll">
                <h2 id="process-heading" class="section-heading">How we work</h2>
                <p class="section-subheading">
                    We keep our process simple and predictable so you always know where things stand and what comes next.
                </p>
                <a href="#contact" class="process-cta-link">
                    Start a conversation
                    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/>
                    </svg>
                </a>
            </div>

            {{-- Right: steps --}}
            <div class="process-steps stagger">
                @foreach(config('clearford.process') as $step)
                    <x-process-step :step="$step" />
                    @if(!$loop->last)
                    <div class="process-divider" aria-hidden="true"></div>
                    @endif
                @endforeach
            </div>

        </div>
    </div>
</section>

{{-- ══ CONTACT ══════════════════════════════════════════════════════════════ --}}
<section id="contact" class="section-pad section-bg-light" aria-labelledby="contact-heading" x-data="contactForm()">
    <div class="container">
        <div class="contact-layout">

            {{-- Left: intro --}}
            <div class="animate-on-scroll contact-sticky">
                <h2 id="contact-heading" class="contact-heading">Let's talk about your project</h2>
                <p class="contact-desc">
                    Fill in the form and we'll get back to you within 1–2 business days. No sales pressure - just a straight conversation about whether we're a good fit.
                </p>
                <div class="contact-email-row">
                    <svg fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
                    </svg>
                    <span>{{ config('clearford.company.email') }}</span>
                </div>
            </div>

            {{-- Right: form --}}
            <div class="animate-on-scroll">

                {{-- Non-JS: server redirected back with success flash --}}
                @if(session('inquiry_success'))
                <div class="alert alert-success" role="alert" aria-live="polite">
                    <div class="alert-success-flex">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        <div>
                            <p><strong>Thanks - we received your inquiry.</strong></p>
                            <p>We'll be in touch within 1–2 business days.</p>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Non-JS: server validation general error --}}
                @if($errors->has('general'))
                <div class="alert alert-error" role="alert">{{ $errors->first('general') }}</div>
                @endif

                {{-- AJAX: success state (hidden until submitted = true) --}}
                <div x-show="submitted" x-cloak class="alert alert-success" role="alert" aria-live="polite">
                    <div class="alert-success-flex">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        <div>
                            <p><strong>Thanks - we received your inquiry.</strong></p>
                            <p>We'll be in touch within 1–2 business days.</p>
                        </div>
                    </div>
                </div>

                {{-- AJAX: general error (hidden until generalError has a value) --}}
                <div x-show="generalError" x-cloak class="alert alert-error" role="alert" aria-live="polite" x-text="generalError"></div>

                {{-- Form: hidden after successful AJAX submission --}}
                <form
                    id="contact-form"
                    x-show="!submitted"
                    method="POST"
                    action="/contact"
                    @submit.prevent="submitForm($el)"
                    novalidate
                    aria-label="Project inquiry form"
                >
                    @csrf
<div class="form-stack">

                        {{-- Name --}}
                        <div class="form-group">
                            <label class="form-label" for="name">
                                Name <span class="form-required" aria-hidden="true">*</span>
                            </label>
                            <input
                                type="text" id="name" name="name"
                                required autocomplete="name"
                                value="{{ old('name') }}"
                                class="form-control"
                                :class="fieldErrors.name ? 'has-error' : ''"
                                aria-required="true"
                                :aria-invalid="fieldErrors.name ? 'true' : 'false'"
                                :aria-describedby="fieldErrors.name ? 'name-err' : null"
                            >
                            @error('name')
                                <span class="form-error" role="alert">{{ $message }}</span>
                            @enderror
                            <span class="form-error" id="name-err" role="alert" x-show="fieldErrors.name" x-text="fieldErrors.name" x-cloak></span>
                        </div>

                        {{-- Company --}}
                        <div class="form-group">
                            <label class="form-label" for="company">
                                Company <span class="form-required" aria-hidden="true">*</span>
                            </label>
                            <input
                                type="text" id="company" name="company"
                                required autocomplete="organization"
                                value="{{ old('company') }}"
                                class="form-control"
                                :class="fieldErrors.company ? 'has-error' : ''"
                                aria-required="true"
                                :aria-invalid="fieldErrors.company ? 'true' : 'false'"
                                :aria-describedby="fieldErrors.company ? 'company-err' : null"
                            >
                            @error('company')
                                <span class="form-error" role="alert">{{ $message }}</span>
                            @enderror
                            <span class="form-error" id="company-err" role="alert" x-show="fieldErrors.company" x-text="fieldErrors.company" x-cloak></span>
                        </div>

                        {{-- Work Email --}}
                        <div class="form-group">
                            <label class="form-label" for="email">
                                Work Email <span class="form-required" aria-hidden="true">*</span>
                            </label>
                            <input
                                type="email" id="email" name="email"
                                required autocomplete="email"
                                value="{{ old('email') }}"
                                class="form-control"
                                :class="fieldErrors.email ? 'has-error' : ''"
                                aria-required="true"
                                :aria-invalid="fieldErrors.email ? 'true' : 'false'"
                                :aria-describedby="fieldErrors.email ? 'email-err' : null"
                            >
                            @error('email')
                                <span class="form-error" role="alert">{{ $message }}</span>
                            @enderror
                            <span class="form-error" id="email-err" role="alert" x-show="fieldErrors.email" x-text="fieldErrors.email" x-cloak></span>
                        </div>

                        {{-- Service --}}
                        <div class="form-group">
                            <label class="form-label" for="service">
                                Service <span class="form-required" aria-hidden="true">*</span>
                            </label>
                            <select
                                id="service" name="service"
                                required
                                class="form-control"
                                :class="fieldErrors.service ? 'has-error' : ''"
                                aria-required="true"
                                :aria-invalid="fieldErrors.service ? 'true' : 'false'"
                                :aria-describedby="fieldErrors.service ? 'service-err' : null"
                            >
                                <option value="" disabled {{ old('service') ? '' : 'selected' }}>Select a service…</option>
                                @foreach(config('clearford.services') as $svc)
                                <option value="{{ $svc['key'] }}" {{ old('service') === $svc['key'] ? 'selected' : '' }}>
                                    {{ $svc['title'] }}
                                </option>
                                @endforeach
                                <option value="not-sure" {{ old('service') === 'not-sure' ? 'selected' : '' }}>Not sure yet</option>
                            </select>
                            @error('service')
                                <span class="form-error" role="alert">{{ $message }}</span>
                            @enderror
                            <span class="form-error" id="service-err" role="alert" x-show="fieldErrors.service" x-text="fieldErrors.service" x-cloak></span>
                        </div>

                        {{-- Project Details --}}
                        <div class="form-group">
                            <label class="form-label" for="project_details">
                                Project Details <span class="form-required" aria-hidden="true">*</span>
                            </label>
                            <textarea
                                id="project_details" name="project_details"
                                rows="5" required minlength="20" maxlength="2000"
                                placeholder="Briefly describe what you're looking for help with…"
                                class="form-control"
                                :class="fieldErrors.project_details ? 'has-error' : ''"
                                aria-required="true"
                                :aria-invalid="fieldErrors.project_details ? 'true' : 'false'"
                                :aria-describedby="fieldErrors.project_details ? 'details-err' : 'details-hint'"
                            >{{ old('project_details') }}</textarea>
                            <span class="form-hint" id="details-hint">Between 20 and 2,000 characters.</span>
                            @error('project_details')
                                <span class="form-error" role="alert">{{ $message }}</span>
                            @enderror
                            <span class="form-error" id="details-err" role="alert" x-show="fieldErrors.project_details" x-text="fieldErrors.project_details" x-cloak></span>
                        </div>

                        {{-- Submit button --}}
                        <div>
                            <button
                                type="submit"
                                class="btn btn-primary btn-block"
                                :disabled="submitting"
                                :aria-busy="submitting.toString()"
                            >
                                <span x-show="submitting" x-cloak class="btn-spinner" aria-hidden="true"></span>
                                <span x-text="submitting ? 'Sending...' : 'Send Inquiry'">Send Inquiry</span>
                            </button>
                        </div>
</div>
                </form>
            </div>

        </div>
    </div></section>

</main>

{{-- ══ FOOTER ═══════════════════════════════════════════════════════════════ --}}
<footer class="site-footer" role="contentinfo">
    <div class="container">
        <div class="footer-inner">
            <div class="footer-grid">

                {{-- Logo + tagline --}}
                <div class="footer-logo">
                    <a href="#home" aria-label="{{ config('clearford.company.name') }} - go to top">
                        <img src="/logo/Logowithoutbg.png" alt="{{ config('clearford.company.name') }}" width="176" height="44" loading="lazy">
                    </a>
                    <p class="footer-tagline">Software, analytics, QA, and operations projects for U.S. companies.</p>
                </div>

                {{-- Navigation --}}
                <nav aria-label="Footer navigation">
                    <p class="footer-col-title">Navigation</p>
                    <ul class="footer-nav-list">
                        @foreach(config('clearford.nav') as $item)
                        <li><a href="{{ $item['href'] }}">{{ $item['label'] }}</a></li>
                        @endforeach
                    </ul>
                </nav>

                {{-- Contact --}}
                <div class="footer-email">
                    <p class="footer-col-title">Contact</p>
                    <a href="mailto:{{ config('clearford.company.email') }}">{{ config('clearford.company.email') }}</a>
                    <div class="footer-legal">
                        @if(config('clearford.footer.show_privacy'))
                        <a href="/privacy">Privacy Policy</a>
                        @else
                        <span>Privacy Policy <em>(coming soon)</em></span>
                        @endif
                        @if(config('clearford.footer.show_terms'))
                        <a href="/terms">Terms of Service</a>
                        @else
                        <span>Terms of Service <em>(coming soon)</em></span>
                        @endif
                    </div>
                </div>

            </div>

            <div class="footer-bottom">
                <p class="footer-copyright">&copy; {{ date('Y') }} {{ config('clearford.footer.copyright_name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</footer>

</x-layout>
