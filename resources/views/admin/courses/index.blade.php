<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-white">Manage Courses</h2>
    </x-slot>

    <div class="mx-auto max-w-4xl px-4 py-8">

        @if(session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200/90 bg-emerald-50 p-3 text-sm text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('admin.courses.create') }}"
           class="mb-6 inline-block rounded-xl border border-red-500/50 bg-gradient-to-r from-red-500 to-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-all duration-200 hover:from-red-400 hover:to-red-500 dark:shadow-lg dark:shadow-red-500/20">
            + New Course
        </a>

        @forelse($courses as $course)
            <div class="mb-4 rounded-2xl border border-slate-200/90 bg-white p-5 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-white/[0.05] dark:shadow-none">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <span class="text-lg font-semibold text-slate-900 dark:text-white">{{ $course->title }}</span>
                        <span class="ml-2 inline-flex rounded-full border px-2 py-0.5 text-xs font-medium
                            {{ $course->level === 'beginner' ? 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/20 dark:text-emerald-400' : '' }}
                            {{ $course->level === 'intermediate' ? 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/20 dark:text-amber-400' : '' }}
                            {{ $course->level === 'pro' ? 'border-red-200 bg-red-50 text-red-800 dark:border-red-500/30 dark:bg-red-500/20 dark:text-red-400' : '' }}">
                            {{ ucfirst($course->level) }}
                        </span>
                        <p class="mt-1 text-sm text-slate-600 dark:text-slate-500">{{ $course->lessons_count }} lesson(s)</p>
                    </div>
                    <div class="flex flex-shrink-0 items-center gap-2 sm:gap-3">
                        <a href="{{ route('admin.courses.edit', $course) }}"
                           class="text-sm font-medium text-slate-600 transition-colors hover:text-red-600 dark:text-slate-400 dark:hover:text-white">Edit</a>
                        <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                              onsubmit="return confirm('Delete this course and all its lessons?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm font-medium text-red-600 transition-colors hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                        </form>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-3 border-t border-slate-200/90 pt-4 dark:border-white/10">
                    <a href="{{ route('admin.courses.show', $course) }}"
                       class="rounded-xl border border-slate-200/90 bg-slate-50 px-4 py-2 text-sm font-medium text-slate-800 transition-all duration-200 hover:bg-slate-100 dark:border-white/10 dark:bg-white/10 dark:text-white dark:hover:bg-white/15">
                        Manage Lessons
                    </a>
                    <a href="{{ route('admin.lessons.create', $course) }}"
                       class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-all duration-200 hover:bg-blue-500">
                        + Add New Lesson
                    </a>
                </div>
            </div>
        @empty
            <p class="text-slate-600 dark:text-slate-500">No courses yet.</p>
        @endforelse
    </div>
</x-app-layout>
