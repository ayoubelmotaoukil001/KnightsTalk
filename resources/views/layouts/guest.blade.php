<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            body { font-family: 'Inter', sans-serif; }
            .chess-pattern {
                background-image:
                    linear-gradient(45deg, rgba(255,255,255,0.015) 25%, transparent 25%),
                    linear-gradient(-45deg, rgba(255,255,255,0.015) 25%, transparent 25%),
                    linear-gradient(45deg, transparent 75%, rgba(255,255,255,0.015) 75%),
                    linear-gradient(-45deg, transparent 75%, rgba(255,255,255,0.015) 75%);
                background-size: 60px 60px;
                background-position: 0 0, 0 30px, 30px -30px, -30px 0px;
            }
        </style>
    </head>
    <body class="antialiased bg-[#0b0d14] text-gray-100">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 chess-pattern">

            <a href="/" class="mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-10 h-10 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-1 0-2.5 1-2.5 1L8 6.5S6.5 7 6 8c-.5 1-.5 2-.5 2L4 12h3l-1 6h12l-1-6h3l-1.5-2s0-1-.5-2c-.5-1-2-1.5-2-1.5L14.5 4S13 3 12 3z"/>
                        <rect x="7" y="18" width="10" height="2" rx="1"/>
                    </svg>
                    <span class="text-2xl font-bold text-white tracking-tight">KnightsTalk</span>
                </div>
            </a>

            <div class="w-full sm:max-w-md px-8 py-8 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl shadow-2xl shadow-black/30">
                {{ $slot }}
            </div>

            <p class="mt-8 text-xs text-gray-600">Master your chess game, one move at a time.</p>
        </div>
    </body>
</html>
