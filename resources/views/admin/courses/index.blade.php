<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">Manage Courses</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-4xl mx-auto">

        @if(session('success'))
            <div class="mb-4 p-3 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl">{{ session('success') }}</div>
        @endif

        <a href="{{ route('admin.courses.create') }}"
           class="inline-block mb-6 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-700 text-white rounded-xl hover:from-emerald-400 hover:to-emerald-600 text-sm font-medium transition-all duration-200 shadow-lg shadow-emerald-500/20">
            + New Course
        </a>

        @forelse($courses as $course)
            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-5 mb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-lg font-semibold text-white">{{ $course->title }}</span>
                        <span class="ml-2 text-xs px-2 py-0.5 rounded-full
                            {{ $course->level === 'beginner' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : '' }}
                            {{ $course->level === 'intermediate' ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30' : '' }}
                            {{ $course->level === 'pro' ? 'bg-red-500/20 text-red-400 border border-red-500/30' : '' }}">
                            {{ ucfirst($course->level) }}
                        </span>
                        <p class="text-sm text-gray-500 mt-1">{{ $course->lessons_count }} lesson(s)</p>
                    </div>
                    <div class="flex gap-2 items-center">
                        <a href="{{ route('admin.courses.edit', $course) }}"
                           class="text-sm text-gray-400 hover:text-white transition-colors">Edit</a>
                        <form method="POST" action="{{ route('admin.courses.destroy', $course) }}"
                              onsubmit="return confirm('Delete this course and all its lessons?')">
                            @csrf @method('DELETE')
                            <button class="text-sm text-red-400 hover:text-red-300 transition-colors">Delete</button>
                        </form>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-white/5 flex items-center gap-3">
                    <a href="{{ route('admin.courses.show', $course) }}"
                       class="px-4 py-2 bg-white/10 text-white text-sm font-medium rounded-xl hover:bg-white/15 transition-all duration-200 border border-white/10">
                        Manage Lessons
                    </a>
                    <a href="{{ route('admin.lessons.create', $course) }}"
                       class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-500 transition-all duration-200">
                        + Add New Lesson
                    </a>
                </div>
            </div>
        @empty
            <p class="text-gray-500">No courses yet.</p>
        @endforelse
    </div>
</x-app-layout>
