<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl font-semibold text-sm text-gray-300 tracking-wide hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-[#0b0d14] disabled:opacity-25 transition-all duration-200']) }}>
    {{ $slot }}
</button>
