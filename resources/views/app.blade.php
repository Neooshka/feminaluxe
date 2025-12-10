<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
        <meta name="description" content="Feminaluxe: Профессиональный перманентный макияж бровей, губ и межресничек. Привлекательный вид на годы вперед." />
        <meta name="keywords" content="перманентный макияж, брови, губы, межресничка, красота, Москва" />
        <meta name="robots" content="index, follow" />
        <meta property="og:title" content="Feminaluxe - Перманентный макияж" />
        <meta property="og:description" content="Преобразите свой взгляд с профессиональными услугами перманентного макияжа." />
        <meta property="og:type" content="website" />
        <meta property="og:url" content="{{ config('app.url') }}" />
        <meta property="og:image" content="{{ asset('og-image.jpg') }}" /> <!-- Assume uploaded -->
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="Feminaluxe" />
        <meta name="twitter:description" content="Услуги перманентного макияжа для женщин." />
        <meta name="twitter:image" content="{{ asset('twitter-image.jpg') }}" />

        <!-- Favicon for branding -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon" />
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}" />
        <link rel="manifest" href="{{ asset('site.webmanifest') }}" /> <!-- For PWA if needed -->

        <!-- Laravel SEO package integration for dynamic meta -->
        {!! seo() !!}

        <!-- Inertia head for dynamic title and other head elements -->
        @inertiaHead

        @routes

        <!-- Vite assets with HMR in dev, production bundles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Preload assets for performance (best practice 2025) -->
        <link rel="preload" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/js/app.js') }}" as="script" />
        <link rel="preload" href="{{ \Illuminate\Support\Facades\Vite::asset('resources/css/app.css') }}" as="style" />

        <!-- Google Fonts or custom for beauty theme (e.g., elegant fonts) -->
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

        <!-- Performance: Defer non-critical JS, async CSS if possible -->
    </head>
    <body class="antialiased bg-beige-100 font-sans text-gray-900 leading-normal">
        <!-- Inertia root for Vue SPA rendering -->
        @inertia

        <!-- Global scripts if needed (e.g., for A/B testing or pixels) -->
        @if (app()->environment('production'))
            <!-- Facebook Pixel for retargeting (selling best practice) -->
            <script>
                !function(f,b,e,v,n,t,s)
                {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
                fbq('init', 'YOUR_PIXEL_ID'); // Replace
                fbq('track', 'PageView');
            </script>
            <noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=YOUR_PIXEL_ID&ev=PageView&noscript=1"/></noscript>
        @endif

        <!-- Accessibility: ARIA live region for dynamic updates -->
        <div aria-live="polite" id="live-region" class="sr-only"></div>
    </body>
</html>
