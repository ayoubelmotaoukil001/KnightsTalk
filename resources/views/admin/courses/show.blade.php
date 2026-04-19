<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-white">{{ $course->title }}</h2>
    </x-slot>

    <div class="mx-auto max-w-4xl px-4 py-8">

        @if(session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200/90 bg-emerald-50 p-3 text-sm text-emerald-800 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-400">
                {{ session('success') }}
            </div>
        @endif

        <a href="{{ route('admin.courses.index') }}" class="mb-4 inline-block text-sm font-medium text-red-600 transition-colors hover:text-red-500 dark:text-red-400 dark:hover:text-red-300">&larr; Back to Courses</a>

        <div class="mb-6 rounded-2xl border border-slate-200/90 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/[0.05] dark:shadow-none">
            <div class="mb-2 flex flex-wrap items-center justify-between gap-2">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $course->title }}</h3>
                <span class="inline-flex rounded-full border px-2 py-0.5 text-xs font-medium
                    {{ $course->level === 'beginner' ? 'border-emerald-200 bg-emerald-50 text-emerald-800 dark:border-emerald-500/30 dark:bg-emerald-500/20 dark:text-emerald-400' : '' }}
                    {{ $course->level === 'intermediate' ? 'border-amber-200 bg-amber-50 text-amber-800 dark:border-amber-500/30 dark:bg-amber-500/20 dark:text-amber-400' : '' }}
                    {{ $course->level === 'pro' ? 'border-red-200 bg-red-50 text-red-800 dark:border-red-500/30 dark:bg-red-500/20 dark:text-red-400' : '' }}">
                    {{ ucfirst($course->level) }}
                </span>
            </div>
            @if($course->description)
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ $course->description }}</p>
            @endif
            <div class="mt-3">
                <a href="{{ route('admin.courses.edit', $course) }}" class="text-sm font-medium text-slate-600 transition-colors hover:text-red-600 dark:text-slate-400 dark:hover:text-white">Edit Course</a>
            </div>
        </div>

        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h4 class="text-lg font-semibold text-slate-900 dark:text-white">Lessons ({{ $course->lessons->count() }})</h4>
            <a href="{{ route('admin.lessons.create', $course) }}"
               class="rounded-xl border border-red-500/50 bg-gradient-to-r from-red-500 to-red-600 px-4 py-2 text-sm font-medium text-white shadow-md transition-all duration-200 hover:from-red-400 hover:to-red-500 dark:shadow-lg dark:shadow-red-500/20">
                + Add New Board Lesson
            </a>
        </div>

        @forelse($course->lessons as $lesson)
            <div class="mb-3 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-slate-200/90 bg-white p-4 shadow-sm dark:border-white/10 dark:bg-white/[0.05] dark:shadow-none">
                <div class="min-w-0">
                    <span class="mr-2 text-xs text-slate-500 dark:text-slate-500">#{{ $lesson->order }}</span>
                    <span class="font-medium text-slate-900 dark:text-white">{{ $lesson->title }}</span>
                    <span class="ml-2 text-xs text-slate-500 dark:text-slate-500">{{ count($lesson->move_descriptions ?? []) }} moves</span>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.lessons.edit', [$course, $lesson]) }}"
                       class="text-sm font-medium text-slate-600 transition-colors hover:text-red-600 dark:text-slate-400 dark:hover:text-white">Edit</a>
                    <form method="POST" action="{{ route('admin.lessons.destroy', [$course, $lesson]) }}"
                          onsubmit="return confirm('Delete this lesson?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-sm font-medium text-red-600 transition-colors hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-slate-600 dark:text-slate-500">No lessons yet. Add one above.</p>
        @endforelse
    </div>
</x-app-layout>
