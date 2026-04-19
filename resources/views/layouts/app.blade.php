<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'KnightsTalk') }}</title>

        <script>
            (function () {
                try {
                    if (localStorage.getItem('kt-theme') === 'light') {
                        document.documentElement.classList.remove('dark');
                    } else {
                        document.documentElement.classList.add('dark');
                    }
                } catch (e) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=playfair-display:400,500,600,700,700i&display=swap" rel="stylesheet" />
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            *, *::before, *::after { box-sizing: border-box; }
            html { scroll-behavior: smooth; }

            h1, h2, h3, h4 {
                font-family: 'Playfair Display', Georgia, serif;
                letter-spacing: -0.01em;
            }

            ::-webkit-scrollbar { width: 4px; height: 4px; }
            ::-webkit-scrollbar-track { background: transparent; }
            html.dark ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.08); border-radius: 99px; }
            html.dark ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.15); }
            html:not(.dark) ::-webkit-scrollbar-thumb { background: rgba(15,23,42,0.12); border-radius: 99px; }
            html:not(.dark) ::-webkit-scrollbar-thumb:hover { background: rgba(15,23,42,0.2); }

            html.dark ::selection { background: rgba(239,68,68,0.25); color: #fff; }
            html:not(.dark) ::selection { background: rgba(239,68,68,0.18); color: #0f172a; }

            .progress-glow { box-shadow: 0 0 8px rgba(245,158,11,0.8), 0 0 16px rgba(245,158,11,0.35); }
            .progress-glow-blue { box-shadow: 0 0 8px rgba(59,130,246,0.7), 0 0 16px rgba(59,130,246,0.3); }

            @keyframes brilliantPulse {
                0%, 100% { box-shadow: 0 0 0 0 rgba(245,158,11,0.4); }
                50% { box-shadow: 0 0 0 8px rgba(245,158,11,0); }
            }
            .brilliant-pulse { animation: brilliantPulse 2s ease-in-out infinite; }
        </style>
        @stack('styles')
    </head>
    <body class="antialiased bg-slate-100 text-slate-900 dark:bg-[#050505] dark:text-slate-200 font-[Inter,sans-serif] tracking-wide">
        <div class="min-h-screen flex flex-col">
            @include('layouts.navigation')

            @isset($header)
                <header class="border-b border-slate-200/80 bg-white/70 backdrop-blur-md dark:border-white/[0.05] dark:bg-white/[0.015]">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>
        <script>
            window.ktToggleTheme = function () {
                var h = document.documentElement;
                if (h.classList.contains('dark')) {
                    h.classList.remove('dark');
                    try { localStorage.setItem('kt-theme', 'light'); } catch (e) {}
                } else {
                    h.classList.add('dark');
                    try { localStorage.setItem('kt-theme', 'dark'); } catch (e) {}
                }
            };
        </script>
        @stack('scripts')
    </body>
</html>
