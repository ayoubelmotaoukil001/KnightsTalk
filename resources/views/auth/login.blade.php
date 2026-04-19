<x-guest-layout>
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <div class="mb-8">
        <p class="text-[10px] font-semibold text-red-500/60 uppercase tracking-[0.2em] mb-1.5">Sign In</p>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-1.5" style="font-family:'Playfair Display',serif;">Welcome back.</h1>
        <p class="text-sm text-slate-500">Sign in to continue your training.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="your@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input id="remember_me" type="checkbox" class="w-3.5 h-3.5 rounded border-slate-300 bg-white text-red-600 focus:ring-red-500/30 focus:ring-offset-0 focus:ring-1 dark:border-white/20 dark:bg-transparent dark:text-red-500 dark:focus:ring-red-500" name="remember">
                <span class="text-xs text-slate-500">{{ __('Remember me') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-xs text-slate-500 hover:text-red-400 transition-colors duration-200" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center py-3 text-sm">
            {{ __('Sign In') }}
        </x-primary-button>

        <p class="text-center text-xs text-slate-600">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-red-400/80 hover:text-red-300 font-medium transition-colors duration-200">Create one</a>
        </p>
    </form>
</x-guest-layout>
