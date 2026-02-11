<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Alföldy Dóra')</title>
    <meta name="description" content="@yield('meta_description', 'Alföldy Dóra sminkes, szempilla és szemöldök stylist.')">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('images/content/favicon.png') }}?v=1" type="image/x-icon">
    <link rel="apple-touch-icon" href="{{ asset('images/content/webclip.png') }}?v=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="body antialiased">
    <div id="top"></div>
    <x-navbar />

    <main class="max-w-[1700px] mr-auto ml-auto" id="main-content">
        @yield('page')
    </main>

    <x-footer />

    <a
        href="#top"
        data-back-to-top
        aria-label="Ugrás az oldal tetejére"
        class="fixed bottom-6 right-6 z-50 inline-flex h-12 w-12 items-center justify-center rounded-full bg-brand-gold text-white shadow-[0_10px_30px_rgba(0,0,0,0.25)] transition-all duration-300"
        style="opacity: 0; pointer-events: none; transform: translateY(6px);"
    >
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6">
            <path d="M12 5.5a1 1 0 0 1 .7.29l6 6a1 1 0 1 1-1.4 1.42L13 8.91V18a1 1 0 1 1-2 0V8.9l-4.3 4.3a1 1 0 0 1-1.4-1.42l6-6A1 1 0 0 1 12 5.5Z"/>
        </svg>
    </a>

    @stack('scripts')
    <script>
        (function () {
            const btn = document.querySelector('[data-back-to-top]');
            if (!btn) return;

            const show = () => {
                const visible = window.scrollY > 400;
                btn.style.opacity = visible ? '1' : '0';
                btn.style.pointerEvents = visible ? 'auto' : 'none';
                btn.style.transform = visible ? 'translateY(0)' : 'translateY(6px)';
            };

            window.addEventListener('scroll', show, { passive: true });
            show();

            btn.addEventListener('click', (e) => {
                e.preventDefault();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        })();
    </script>
</body>
</html>
