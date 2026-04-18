<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-700 border border-emerald-600/50 rounded-xl font-semibold text-sm text-white tracking-wide hover:from-emerald-400 hover:to-emerald-600 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-[#0b0d14] active:from-emerald-600 active:to-emerald-800 transition-all duration-200 shadow-lg shadow-emerald-500/20']) }}>
    {{ $slot }}
</button>
