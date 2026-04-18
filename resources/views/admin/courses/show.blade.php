<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">{{ $course->title }}</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-4xl mx-auto">

        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl">{{ session('success') }}</div>
        @endif

        <a href="{{ route('admin.courses.index') }}" class="text-emerald-400 hover:text-emerald-300 text-sm mb-4 inline-block transition-colors">&larr; Back to Courses</a>

        <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 mb-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-lg font-bold text-white">{{ $course->title }}</h3>
                <span class="text-xs px-2 py-0.5 rounded-full
                    {{ $course->level === 'beginner' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : '' }}
                    {{ $course->level === 'intermediate' ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30' : '' }}
                    {{ $course->level === 'pro' ? 'bg-red-500/20 text-red-400 border border-red-500/30' : '' }}">
                    {{ ucfirst($course->level) }}
                </span>
            </div>
            @if($course->description)
                <p class="text-gray-400 text-sm">{{ $course->description }}</p>
            @endif
            <div class="mt-3">
                <a href="{{ route('admin.courses.edit', $course) }}" class="text-sm text-gray-400 hover:text-white transition-colors">Edit Course</a>
            </div>
        </div>

        <div class="flex items-center justify-between mb-4">
            <h4 class="text-lg font-semibold text-white">Lessons ({{ $course->lessons->count() }})</h4>
            <a href="{{ route('admin.lessons.create', $course) }}"
               class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-emerald-700 text-white rounded-xl hover:from-emerald-400 hover:to-emerald-600 text-sm font-medium transition-all duration-200 shadow-lg shadow-emerald-500/20">
                + Add New Board Lesson
            </a>
        </div>

        @forelse($course->lessons as $lesson)
            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 mb-3 flex items-center justify-between">
                <div>
                    <span class="text-xs text-gray-500 mr-2">#{{ $lesson->order }}</span>
                    <span class="font-medium text-white">{{ $lesson->title }}</span>
                    <span class="text-xs text-gray-500 ml-2">{{ count($lesson->move_descriptions ?? []) }} moves</span>
                </div>
                <div class="flex gap-3 items-center">
                    <a href="{{ route('admin.lessons.edit', [$course, $lesson]) }}"
                       class="text-sm text-gray-400 hover:text-white transition-colors">Edit</a>
                    <form method="POST" action="{{ route('admin.lessons.destroy', [$course, $lesson]) }}"
                          onsubmit="return confirm('Delete this lesson?')">
                        @csrf @method('DELETE')
                        <button class="text-sm text-red-400 hover:text-red-300 transition-colors">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="text-gray-500">No lessons yet. Add one above.</p>
        @endforelse
    </div>
</x-app-layout>
