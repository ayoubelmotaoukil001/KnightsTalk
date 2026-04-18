<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2.5 bg-red-600/80 border border-red-500/50 rounded-xl font-semibold text-sm text-white tracking-wide hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 focus:ring-offset-[#0b0d14] transition-all duration-200']) }}>
    {{ $slot }}
</button>
