<x-guest-layout>

    <div class="mb-8">
        <p class="text-[10px] font-semibold text-red-500/60 uppercase tracking-[0.2em] mb-1.5">Create Account</p>
        <h1 class="text-3xl font-bold text-slate-900 dark:text-white mb-1.5" style="font-family:'Playfair Display',serif;">Join KnightsTalk.</h1>
        <p class="text-sm text-slate-500">Start your chess journey today.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Your name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="your@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center py-3 text-sm">
            {{ __('Create Account') }}
        </x-primary-button>

        <p class="text-center text-xs text-slate-600">
            Already registered?
            <a href="{{ route('login') }}" class="text-red-400/80 hover:text-red-300 font-medium transition-colors duration-200">Sign in</a>
        </p>
    </form>
</x-guest-layout>
