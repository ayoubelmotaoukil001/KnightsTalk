<x-guest-layout>
    <h2 class="text-2xl font-bold text-white text-center mb-1">Reset password</h2>
    <p class="text-gray-500 text-sm text-center mb-6">Enter your email and we'll send you a reset link.</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="your@email.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            {{ __('Email Password Reset Link') }}
        </x-primary-button>

        <p class="text-center text-sm text-gray-500">
            <a href="{{ route('login') }}" class="text-emerald-400 hover:text-emerald-300 font-medium transition-colors">Back to Sign In</a>
        </p>
    </form>
</x-guest-layout>
