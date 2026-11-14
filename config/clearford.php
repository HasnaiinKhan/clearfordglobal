<?php

/*
|--------------------------------------------------------------------------
| Clearford Global Site Content
|--------------------------------------------------------------------------
|
| This file contains all editable site content and configuration.
| Update this file to change services, copy, SEO tags, and other
| site-wide settings without touching Blade templates.
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Company Details
    |--------------------------------------------------------------------------
    */

    'company' => [
        'name'         => 'Clearford Global',
        'tagline'      => 'Build better systems. Run better operations.',
        'intro'        => 'Clearford Global partners with U.S. companies to deliver software development, data analytics, QA testing, and business operations projects. We work as an extension of your team - focused on outcomes, documentation, and clean handoffs.',
        'intro_detail' => 'Whether you need to build a new internal tool, clean up your reporting, or add testing coverage before a release, we bring the right skills and a structured approach to get it done.',
        'email'        => env('COMPANY_CONTACT_EMAIL', 'hello@clearfordglobal.com'),
        'url'          => env('APP_URL', 'https://clearfordglobal.com'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SEO & Open Graph
    |--------------------------------------------------------------------------
    */

    'seo' => [
        'title'          => 'Clearford Global - Software, Analytics & Operations for U.S. Companies',
        'description'    => 'Clearford Global delivers software development, data analytics, QA testing, and business operations projects for U.S. companies. Structured approach, clean handoffs.',
        'og_image'       => '/logo/Logowithbg.jpeg',
        'twitter_handle' => '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Services
    |--------------------------------------------------------------------------
    |
    | Each service has: key, title, description, bullets, and an SVG icon name.
    | The 'key' must match the values in ContactFormRequest's service validation.
    |
    */

    'services' => [
        [
            'key'         => 'software-development',
            'title'       => 'Software Development',
            'description' => 'We build web applications, internal tools, APIs, and system integrations that fit your existing workflows and scale with your business.',
            'bullets'     => [
                'Web application development',
                'Internal business tools',
                'REST API development & integration',
                'Third-party system integrations',
            ],
            'icon'        => 'code',
        ],
        [
            'key'         => 'data-analytics',
            'title'       => 'Data Analytics & Reporting',
            'description' => 'Turn raw data into clear, actionable reports. We help teams get better visibility into their numbers without overhauling their entire stack.',
            'bullets'     => [
                'Business intelligence dashboards',
                'SQL-based reporting & analysis',
                'Data preparation & transformation',
                'Automated reporting pipelines',
            ],
            'icon'        => 'chart',
        ],
        [
            'key'         => 'software-qa',
            'title'       => 'Software QA & Testing',
            'description' => 'Catch issues before they reach production. We provide structured testing coverage across manual, automated, and API testing scenarios.',
            'bullets'     => [
                'Manual functional testing',
                'API testing & validation',
                'Regression test suites',
                'Test automation implementation',
            ],
            'icon'        => 'shield-check',
        ],
        [
            'key'         => 'business-analysis',
            'title'       => 'Business Analysis & Operations',
            'description' => 'We help teams define what they need, map how they work, and implement the changes that make operations run more smoothly.',
            'bullets'     => [
                'Requirements gathering & documentation',
                'Process mapping & workflow analysis',
                'Workflow improvement initiatives',
                'Implementation support & change management',
            ],
            'icon'        => 'briefcase',
        ],
        [
            'key'         => 'healthcare-technology',
            'title'       => 'Healthcare Technology & Operations',
            'description' => 'Specialist support for healthcare-adjacent software projects, covering reporting, testing, and administrative workflow improvements.',
            'bullets'     => [
                'Healthcare software reporting',
                'Software testing for clinical platforms',
                'Administrative workflow support',
                'Documentation & requirements work',
            ],
            'icon'        => 'heart-pulse',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | How We Work
    |--------------------------------------------------------------------------
    */

    'process' => [
        [
            'step'        => 1,
            'title'       => 'Agree on scope and deliverables',
            'description' => 'We start every engagement by defining exactly what will be built or delivered, what success looks like, and what falls outside scope. No ambiguity.',
        ],
        [
            'step'        => 2,
            'title'       => 'Review progress at milestones',
            'description' => 'Work is broken into clear milestones with regular check-ins so you can review progress, give feedback, and stay aligned without micromanaging.',
        ],
        [
            'step'        => 3,
            'title'       => 'Hand over completed work with documentation',
            'description' => 'Every project ends with a clean handoff - working deliverables, documentation, and a walkthrough so your team can own and maintain what was built.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    'nav' => [
        ['label' => 'Home',         'href' => '#home'],
        ['label' => 'Services',     'href' => '#services'],
        ['label' => 'How We Work',  'href' => '#process'],
        ['label' => 'Contact',      'href' => '#contact'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer
    |--------------------------------------------------------------------------
    */

    'footer' => [
        'copyright_name' => 'Clearford Global',
        // Set to true once privacy policy page is created
        'show_privacy'   => false,
        // Set to true once terms page is created
        'show_terms'     => false,
    ],

];
