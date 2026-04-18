<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">Courses</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-4xl mx-auto">

        @forelse($courses as $course)
            <a href="{{ route('courses.show', $course) }}"
               class="block bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-5 mb-4 hover:bg-white/10 hover:border-white/20 transition-all duration-200">

                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-lg font-semibold text-white">{{ $course->title }}</h3>
                    <span class="text-xs px-3 py-1 rounded-full font-medium
                        {{ $course->level === 'beginner'     ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : '' }}
                        {{ $course->level === 'intermediate' ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30'   : '' }}
                        {{ $course->level === 'pro'          ? 'bg-red-500/20 text-red-400 border border-red-500/30'            : '' }}">
                        {{ ucfirst($course->level) }}
                    </span>
                </div>

                @if($course->description)
                    <p class="text-sm text-gray-400 mb-3">{{ Str::limit($course->description, 120) }}</p>
                @endif

                <div class="mt-2">
                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                        <span>{{ $course->completed_count }} / {{ $course->lessons_count }} lessons completed</span>
                        <span class="font-semibold {{ $course->progress_percentage === 100 ? 'text-emerald-400' : 'text-blue-400' }}">{{ $course->progress_percentage }}%</span>
                    </div>
                    <div class="w-full bg-white/5 rounded-full h-2">
                        <div class="h-2 rounded-full transition-all duration-500
                            {{ $course->progress_percentage === 100 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]' : 'bg-blue-500' }}"
                             style="width: {{ $course->progress_percentage }}%">
                        </div>
                    </div>
                </div>

            </a>
        @empty
            <p class="text-gray-500 text-center py-12">No courses available yet.</p>
        @endforelse
    </div>
</x-app-layout>
