<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-sm text-slate-600 leading-tight tracking-widest uppercase dark:text-slate-500">{{ $course->title }}</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-4xl mx-auto">

        <a href="{{ route('courses.index') }}" class="mb-6 inline-flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-widest text-slate-500 transition-colors duration-200 hover:text-red-600 dark:text-slate-600 dark:hover:text-red-400">
            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            All Courses
        </a>

        <div class="mb-6 rounded-3xl border border-slate-200/90 bg-white p-7 shadow-sm dark:bg-white/[0.03] dark:border-white/[0.07] dark:shadow-none">
            <div class="mb-3 flex items-start gap-3">
                <h1 class="flex-1 text-2xl font-bold text-slate-900 dark:text-white">{{ $course->title }}</h1>
                <span class="flex-shrink-0 rounded-full border px-2.5 py-1 text-[10px] font-semibold uppercase tracking-widest
                    {{ $course->level === 'beginner' ? 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400' : '' }}
                    {{ $course->level === 'intermediate' ? 'border-red-200 bg-red-50 text-red-700 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400' : '' }}
                    {{ $course->level === 'pro' ? 'border-slate-200 bg-slate-100 text-slate-700 dark:border-white/[0.12] dark:bg-white/[0.07] dark:text-slate-300' : '' }}">
                    {{ ucfirst($course->level) }}
                </span>
            </div>

            @if($course->description)
                <p class="mb-5 text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ $course->description }}</p>
            @endif

            <div class="mb-1.5 flex justify-between text-[11px] text-slate-500 dark:text-slate-600">
                <span>Your Progress</span>
                <span class="font-bold {{ $progress_percentage === 100 ? 'text-amber-600 dark:text-amber-400' : 'text-blue-600 dark:text-blue-400' }}">{{ $completed }} / {{ $total }} — {{ $progress_percentage }}%</span>
            </div>
            <div class="h-[2px] w-full rounded-full bg-slate-200 dark:bg-white/[0.04]">
                <div class="h-[2px] rounded-full transition-all duration-500 {{ $progress_percentage === 100 ? 'bg-amber-500 progress-glow dark:bg-amber-400' : 'bg-blue-500 progress-glow-blue dark:bg-blue-400' }}"
                     style="width: {{ $progress_percentage }}%"></div>
            </div>
        </div>

        <p class="mb-3 px-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-slate-600">Lessons</p>

        @forelse($course->lessons as $index => $lesson)
            @php $done = in_array($lesson->id, $completedIds); @endphp
            <a href="{{ route('courses.lesson', [$course, $lesson]) }}"
               class="group mb-2 flex items-center gap-4 rounded-2xl border px-5 py-4 transition-all duration-300
                   {{ $done
                       ? 'border-amber-200/80 bg-amber-50/30 hover:border-amber-300 dark:border-amber-500/15 dark:bg-transparent dark:hover:border-amber-500/30 dark:hover:bg-amber-500/[0.03]'
                       : 'border-slate-200/90 bg-white shadow-sm hover:border-red-300 hover:bg-slate-50 dark:border-white/[0.07] dark:bg-white/[0.03] dark:shadow-none dark:hover:border-red-500/20 dark:hover:bg-white/[0.05]' }}">

                @if($done)
                    <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border border-amber-300 bg-amber-100 text-sm font-bold text-amber-700 dark:border-amber-500/25 dark:bg-amber-500/15 dark:text-amber-400"
                          style="box-shadow: 0 0 12px rgba(245,158,11,0.2);">✓</span>
                @else
                    <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full border border-transparent bg-slate-100 text-xs font-bold text-slate-600 transition-all duration-300 group-hover:border-red-200 group-hover:bg-red-50 group-hover:text-red-600 dark:bg-white/[0.04] dark:text-slate-600 dark:group-hover:border-red-500/20 dark:group-hover:bg-red-500/10 dark:group-hover:text-red-400">{{ $index + 1 }}</span>
                @endif

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-slate-800 transition-colors duration-200 group-hover:text-slate-950 dark:text-slate-200 dark:group-hover:text-white">{{ $lesson->title }}</p>
                    <p class="text-[11px] text-slate-500 dark:text-slate-600">{{ count($lesson->move_descriptions ?? []) }} moves</p>
                </div>

                <span class="flex-shrink-0 text-[11px] font-semibold transition-colors duration-200 {{ $done ? 'text-amber-700 dark:text-amber-500/60' : 'text-slate-500 group-hover:text-slate-700 dark:text-slate-600 dark:group-hover:text-slate-400' }}">
                    {{ $done ? 'Done' : 'Start →' }}
                </span>
            </a>
        @empty
            <p class="py-10 text-center text-sm text-slate-500 dark:text-slate-600">No lessons in this course yet.</p>
        @endforelse

    </div>
</x-app-layout>
