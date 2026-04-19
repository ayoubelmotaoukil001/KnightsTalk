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
            h1, h2, h3 { font-family: 'Playfair Display', Georgia, serif; }
            html.dark ::selection { background: rgba(239,68,68,0.25); color: #fff; }
            html:not(.dark) ::selection { background: rgba(239,68,68,0.18); color: #0f172a; }
            .chess-grid {
                background-image:
                    linear-gradient(45deg, rgba(255,255,255,0.018) 25%, transparent 25%),
                    linear-gradient(-45deg, rgba(255,255,255,0.018) 25%, transparent 25%),
                    linear-gradient(45deg, transparent 75%, rgba(255,255,255,0.018) 75%),
                    linear-gradient(-45deg, transparent 75%, rgba(255,255,255,0.018) 75%);
                background-size: 72px 72px;
                background-position: 0 0, 0 36px, 36px -36px, -36px 0px;
            }
        </style>
    </head>
    <body class="antialiased bg-slate-100 text-slate-900 dark:bg-[#050505] dark:text-slate-200">

        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2">

            {{-- LEFT: Brand panel (always dark) --}}
            <div class="hidden lg:flex flex-col justify-between relative overflow-hidden bg-[#030303] chess-grid p-12">

                <div class="absolute inset-0 bg-gradient-to-br from-red-900/20 via-transparent to-transparent pointer-events-none"></div>
                <div class="absolute bottom-0 right-0 w-96 h-96 bg-amber-500/5 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-1 0-2.5 1-2.5 1L8 6.5S6.5 7 6 8c-.5 1-.5 2-.5 2L4 12h3l-1 6h12l-1-6h3l-1.5-2s0-1-.5-2c-.5-1-2-1.5-2-1.5L14.5 4S13 3 12 3z"/>
                            <rect x="7" y="18" width="10" height="2" rx="1"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold text-white tracking-tight" style="font-family:'Playfair Display',serif;">KnightsTalk</span>
                </div>

                <div class="relative z-10 flex flex-col items-center justify-center flex-1 py-16 px-8">
                    <div class="h-px w-20 bg-gradient-to-r from-transparent via-white/20 to-transparent" aria-hidden="true"></div>
                    <p class="mt-10 text-center text-sm text-white/35 leading-relaxed max-w-xs">
                        Structured lessons, puzzles, and practice in one place.
                    </p>
                </div>

                <div class="relative z-10">
                    <p class="text-lg text-white/50 italic leading-relaxed" style="font-family:'Playfair Display',serif;">"Chess is life in miniature."</p>
                    <p class="text-xs text-white/25 mt-2 tracking-widest uppercase">— Garry Kasparov</p>
                </div>
            </div>

            {{-- RIGHT: Form panel --}}
            <div class="relative flex flex-col items-center justify-center px-8 py-12 bg-slate-50 dark:bg-[#050505]">

                <div class="absolute top-4 right-4 sm:top-6 sm:right-8">
                    <button type="button" onclick="window.ktToggleTheme()" title="Toggle light / dark mode"
                            class="p-2 rounded-xl border border-slate-200/90 text-slate-600 hover:bg-slate-100 transition-all duration-300 dark:border-white/[0.08] dark:text-slate-400 dark:hover:bg-white/[0.06] dark:hover:text-white">
                        <span class="block dark:hidden"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg></span>
                        <span class="hidden dark:block"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg></span>
                    </button>
                </div>

                <a href="/" class="mb-10 lg:hidden">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-red-500/10 border border-red-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-500 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-1 0-2.5 1-2.5 1L8 6.5S6.5 7 6 8c-.5 1-.5 2-.5 2L4 12h3l-1 6h12l-1-6h3l-1.5-2s0-1-.5-2c-.5-1-2-1.5-2-1.5L14.5 4S13 3 12 3z"/>
                                <rect x="7" y="18" width="10" height="2" rx="1"/>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-slate-900 dark:text-white" style="font-family:'Playfair Display',serif;">KnightsTalk</span>
                    </div>
                </a>

                <div class="w-full max-w-sm">
                    {{ $slot }}
                </div>

                <p class="mt-10 text-xs text-slate-400 tracking-widest uppercase dark:text-white/20">Master the game. One move at a time.</p>
            </div>

        </div>

        <script>
            window.ktToggleTheme = window.ktToggleTheme || function () {
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
    </body>
</html>
