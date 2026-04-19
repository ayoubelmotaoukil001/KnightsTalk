<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-sm text-slate-600 leading-tight tracking-widest uppercase dark:text-slate-500">Puzzles</h2>
    </x-slot>

    <div class="mx-auto max-w-3xl px-4 py-8">

        @if($total > 0)
        <div class="mb-6 rounded-3xl border border-slate-200/90 bg-white p-6 shadow-sm dark:bg-white/[0.03] dark:border-white/[0.07] dark:shadow-none">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Your Progress</h3>
                    <p class="mt-0.5 text-xs text-slate-600 dark:text-slate-600">
                        <span class="font-bold text-slate-800 dark:text-slate-300">{{ $solved }}</span> / {{ $total }} puzzles solved
                    </p>
                </div>
                @if($percent === 100)
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-300 bg-amber-50 px-3 py-1.5 text-[11px] font-bold text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400"
                          style="box-shadow: 0 0 16px rgba(245,158,11,0.15);">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                        All Solved
                    </span>
                @else
                    <span class="text-2xl font-extrabold tabular-nums text-blue-600 dark:text-blue-400">{{ $percent }}%</span>
                @endif
            </div>
            <div class="h-[2px] w-full rounded-full bg-slate-200 dark:bg-white/[0.04]">
                <div class="h-[2px] rounded-full transition-all duration-700 {{ $percent === 100 ? 'bg-amber-500 progress-glow dark:bg-amber-400' : 'bg-blue-500 progress-glow-blue dark:bg-blue-400' }}"
                     style="width: {{ $percent }}%"></div>
            </div>
        </div>
        @endif

        @if ($puzzles->isEmpty())
            <div class="py-24 text-center">
                <p class="mb-4 select-none text-4xl text-slate-300 dark:text-slate-600">♜</p>
                <p class="text-slate-500 dark:text-slate-600">No puzzles yet. Check back later.</p>
            </div>
        @else
            <div class="space-y-2">
                @foreach ($puzzles as $puzzle)
                    @php $solved_this = in_array($puzzle->id, $completedIds); @endphp
                    <div class="group flex items-center gap-4 rounded-2xl border px-5 py-3.5 transition-all duration-300
                        {{ $solved_this
                            ? 'border-amber-200/80 bg-amber-50/40 hover:border-amber-300 dark:border-amber-500/15 dark:bg-transparent dark:hover:border-amber-500/25 dark:hover:bg-amber-500/[0.025]'
                            : 'border-slate-200/90 bg-white shadow-sm hover:border-red-300 dark:border-white/[0.07] dark:bg-white/[0.03] dark:shadow-none dark:hover:border-red-500/20 dark:hover:bg-white/[0.04]' }}">

                        @if($solved_this)
                            <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border border-amber-300 bg-amber-100 text-sm font-bold text-amber-700 dark:border-amber-500/25 dark:bg-amber-500/15 dark:text-amber-400"
                                  style="box-shadow: 0 0 10px rgba(245,158,11,0.2);">✓</span>
                        @else
                            <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-slate-100 text-xs font-semibold text-slate-600 transition-all duration-200 group-hover:bg-slate-200 dark:bg-white/[0.04] dark:text-slate-600 dark:group-hover:bg-white/[0.07]">
                                {{ $loop->iteration }}
                            </span>
                        @endif

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium {{ $solved_this ? 'text-slate-600 dark:text-slate-400' : 'text-slate-800 group-hover:text-slate-950 dark:text-slate-200 dark:group-hover:text-white' }} transition-colors duration-200">{{ $puzzle->title }}</p>
                            <div class="mt-0.5 flex items-center gap-2">
                                <span class="text-[11px] text-slate-500 dark:text-slate-600">{{ $puzzle->difficulty }}</span>
                                @if($solved_this)
                                    <span class="text-[11px] font-medium text-amber-700 dark:text-amber-500/60">· Solved</span>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('puzzles.play', $puzzle) }}"
                           class="flex-shrink-0 rounded-xl px-3.5 py-1.5 text-xs font-semibold transition-all duration-300
                               {{ $solved_this
                                   ? 'border border-slate-200/90 bg-white text-slate-600 hover:border-slate-300 hover:text-slate-800 dark:border-white/[0.08] dark:bg-transparent dark:text-slate-600 dark:hover:border-white/[0.15] dark:hover:text-slate-300'
                                   : 'border border-red-500/50 text-red-600 hover:bg-red-500 hover:text-white hover:border-red-500 hover:shadow-md dark:border-red-500/40 dark:text-red-400 dark:hover:shadow-[0_0_16px_rgba(239,68,68,0.25)]' }}">
                            {{ $solved_this ? 'Retry' : 'Solve' }}
                        </a>

                    </div>
                @endforeach
            </div>
        @endif

    </div>
</x-app-layout>
