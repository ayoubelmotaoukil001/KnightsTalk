<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-sm text-slate-600 leading-tight tracking-widest uppercase dark:text-slate-500">Courses</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-4xl mx-auto">

        @forelse($courses as $course)
            <a href="{{ route('courses.show', $course) }}"
               class="group mb-3 block rounded-3xl border border-slate-200/90 bg-white p-6 shadow-sm transition-all duration-500 hover:border-red-300 hover:bg-slate-50 dark:bg-white/[0.03] dark:border-white/[0.07] dark:shadow-none dark:hover:border-red-500/20 dark:hover:bg-white/[0.05]">

                <div class="mb-2 flex items-start justify-between gap-4">
                    <h3 class="text-lg font-semibold text-slate-900 transition-colors duration-300 group-hover:text-red-600 dark:text-white dark:group-hover:text-red-300">{{ $course->title }}</h3>
                    <span class="flex-shrink-0 rounded-full border px-2.5 py-1 text-[10px] font-semibold tracking-widest uppercase
                        {{ $course->level === 'beginner'     ? 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400' : '' }}
                        {{ $course->level === 'intermediate' ? 'border-red-200 bg-red-50 text-red-700 dark:border-red-500/20 dark:bg-red-500/10 dark:text-red-400'       : '' }}
                        {{ $course->level === 'pro'          ? 'border-slate-200 bg-slate-100 text-slate-700 dark:border-white/[0.12] dark:bg-white/[0.07] dark:text-slate-300'  : '' }}">
                        {{ ucfirst($course->level) }}
                    </span>
                </div>

                @if($course->description)
                    <p class="mb-4 text-sm leading-relaxed text-slate-600 dark:text-slate-500">{{ Str::limit($course->description, 120) }}</p>
                @endif

                <div>
                    <div class="mb-1.5 flex justify-between text-[11px] text-slate-500 dark:text-slate-600">
                        <span>{{ $course->completed_count }} / {{ $course->lessons_count }} lessons</span>
                        <span class="font-bold {{ $course->progress_percentage === 100 ? 'text-amber-600 dark:text-amber-400' : 'text-blue-600 dark:text-blue-400' }}">{{ $course->progress_percentage }}%</span>
                    </div>
                    <div class="h-[2px] w-full rounded-full bg-slate-200 dark:bg-white/[0.04]">
                        <div class="h-[2px] rounded-full transition-all duration-500 {{ $course->progress_percentage === 100 ? 'bg-amber-500 progress-glow dark:bg-amber-400' : 'bg-blue-500 progress-glow-blue dark:bg-blue-400' }}"
                             style="width: {{ $course->progress_percentage }}%"></div>
                    </div>
                </div>

            </a>
        @empty
            <div class="py-24 text-center">
                <p class="text-slate-500 dark:text-slate-600">No courses available yet.</p>
            </div>
        @endforelse

    </div>
</x-app-layout>
