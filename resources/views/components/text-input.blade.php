@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full bg-white border border-slate-200 text-slate-900 placeholder-slate-400 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-red-400 focus:ring-2 focus:ring-red-500/15 transition-all duration-300 disabled:opacity-40 dark:bg-transparent dark:border-white/[0.10] dark:text-slate-100 dark:placeholder-slate-600 dark:focus:border-red-500/50 dark:focus:ring-red-500/10 dark:focus:shadow-[0_0_0_4px_rgba(239,68,68,0.08)]']) }}>
