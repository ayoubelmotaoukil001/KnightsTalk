<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-2.5 bg-transparent border border-red-500/50 rounded-xl font-semibold text-sm text-red-600 tracking-wide hover:bg-red-500 hover:text-white hover:border-red-500 hover:shadow-[0_0_20px_rgba(239,68,68,0.25)] focus:outline-none focus:ring-2 focus:ring-red-500/30 active:bg-red-600 transition-all duration-300 disabled:opacity-40 disabled:cursor-not-allowed dark:text-red-400 dark:hover:shadow-[0_0_20px_rgba(239,68,68,0.3)]']) }}>
    {{ $slot }}
</button>
