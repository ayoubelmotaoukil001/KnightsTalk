@props(['active'])

@php
$classes = ($active ?? false)
    ? 'inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium text-red-600 bg-red-500/10 border border-red-200 dark:text-red-400 dark:bg-red-500/[0.08] dark:border-red-500/20 transition-all duration-300'
    : 'inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-transparent hover:border-slate-200/80 dark:text-slate-500 dark:hover:text-slate-100 dark:hover:bg-white/[0.04] dark:hover:border-white/[0.07] transition-all duration-300';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>{{ $slot }}</a>
