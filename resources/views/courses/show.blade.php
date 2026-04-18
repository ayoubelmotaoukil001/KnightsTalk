<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">{{ $course->title }}</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-4xl mx-auto">

        <a href="{{ route('courses.index') }}" class="text-emerald-400 hover:text-emerald-300 text-sm mb-4 inline-block transition-colors">&larr; All Courses</a>

        <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6 mb-6">
            <div class="flex items-center gap-3 mb-2">
                <h3 class="text-xl font-bold text-white">{{ $course->title }}</h3>
                <span class="text-xs px-2 py-0.5 rounded-full
                    {{ $course->level === 'beginner' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : '' }}
                    {{ $course->level === 'intermediate' ? 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/30' : '' }}
                    {{ $course->level === 'pro' ? 'bg-red-500/20 text-red-400 border border-red-500/30' : '' }}">
                    {{ ucfirst($course->level) }}
                </span>
            </div>

            @if($course->description)
                <p class="text-gray-400 mb-4">{{ $course->description }}</p>
            @endif

            <div class="flex justify-between text-sm text-gray-400 mb-1">
                <span>Your Progress</span>
                <span class="font-semibold {{ $progress_percentage === 100 ? 'text-emerald-400' : 'text-blue-400' }}">{{ $completed }} / {{ $total }} &mdash; {{ $progress_percentage }}%</span>
            </div>
            <div class="w-full bg-white/5 rounded-full h-3">
                <div class="h-3 rounded-full transition-all duration-500 {{ $progress_percentage === 100 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]' : 'bg-blue-500' }}"
                     style="width: {{ $progress_percentage }}%"></div>
            </div>
        </div>

        <h4 class="text-lg font-semibold text-white mb-4">Lessons</h4>

        @forelse($course->lessons as $index => $lesson)
            @php $done = in_array($lesson->id, $completedIds); @endphp
            <a href="{{ route('courses.lesson', [$course, $lesson]) }}"
               class="block bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 mb-3 hover:bg-white/10 hover:border-white/20 transition-all duration-200">
                <div class="flex items-center gap-3">
                    @if($done)
                        <span class="w-8 h-8 bg-emerald-500 text-white rounded-full flex items-center justify-center text-sm font-bold shadow-lg shadow-emerald-500/30">&#x2713;</span>
                    @else
                        <span class="w-8 h-8 bg-white/10 text-gray-400 rounded-full flex items-center justify-center text-sm font-bold">{{ $index + 1 }}</span>
                    @endif
                    <div class="flex-1">
                        <p class="font-medium text-white">{{ $lesson->title }}</p>
                        <p class="text-xs text-gray-500">{{ count($lesson->move_descriptions ?? []) }} moves</p>
                    </div>
                    <span class="text-xs {{ $done ? 'text-emerald-400' : 'text-gray-500' }}">
                        {{ $done ? 'Completed' : 'Start' }}
                    </span>
                </div>
            </a>
        @empty
            <p class="text-gray-500">No lessons in this course yet.</p>
        @endforelse
    </div>
</x-app-layout>
