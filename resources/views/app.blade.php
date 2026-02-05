<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="index, follow" />
        <meta name="yandex-verification" content="9762190435bcc6ec" />

        <!-- Dynamic SEO render -->
        @if (isset($SEOData))
            {!! seo($SEOData) !!}
        @else
            {!! seo() !!}
        @endif

        <!-- Favicon for branding -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />
        <link rel="manifest" href="{{ asset('site.webmanifest') }}" /> <!-- For PWA if needed -->

        @routes

        <!-- Vite assets with HMR in dev, production bundles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Inertia head for dynamic title and other head elements -->
        @inertiaHead

        <!-- Google Fonts for beauty theme (elegant fonts) -->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet" />

        <!-- Analytics for selling site (Google Analytics 4 - GA4) -->
        @if (app()->environment('production'))
            <!-- Google tag (gtag.js) -->
            <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script> <!-- Replace with real ID -->
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag(){dataLayer.push(arguments);}
                gtag('js', new Date());
                gtag('config', 'G-XXXXXXXXXX');
            </script>
        @endif
    </head>
    <body class="antialiased bg-beige-100 font-sans text-gray-900 leading-normal">
        <!-- Inertia root for Vue SPA rendering -->
        @inertia

        <!-- Accessibility: ARIA live region -->
        <div aria-live="polite" id="live-region" class="sr-only"></div>
    </body>
</html>
