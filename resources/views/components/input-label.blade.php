@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-[10px] font-semibold text-slate-600 uppercase tracking-[0.12em] mb-2 dark:text-slate-500']) }}>
    {{ $value ?? $slot }}
</label>
