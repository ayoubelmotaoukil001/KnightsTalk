@props(['active'])

@php
$classes = ($active ?? false)
    ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-red-500 text-start text-base font-medium text-red-600 bg-red-50 dark:text-red-400 dark:border-red-400 dark:bg-red-500/10 transition-all duration-200'
    : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-100 hover:border-slate-300 dark:text-gray-400 dark:hover:text-white dark:hover:bg-white/5 dark:hover:border-gray-600 transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
