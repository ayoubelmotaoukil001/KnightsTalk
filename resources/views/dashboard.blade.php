<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-7xl mx-auto">

        <div class="mb-8 bg-gradient-to-r from-emerald-600/20 to-blue-600/10 border border-emerald-500/20 rounded-2xl px-8 py-6 backdrop-blur-md">
            <p class="text-gray-400 text-sm">Welcome back,</p>
            <h1 class="text-white text-2xl font-bold mt-1">{{ $user->name }}</h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6">
                <h2 class="font-bold text-white mb-4">My Courses</h2>
                @if(count($courseProgress) === 0)
                    <p class="text-gray-500 text-sm py-6 text-center">No courses started yet.</p>
                    <a href="{{ route('courses.index') }}" class="block text-center text-sm text-emerald-400 hover:text-emerald-300 transition-colors">Browse Courses</a>
                @else
                    @foreach($courseProgress as $cp)
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-1">
                                <a href="{{ $cp['url'] }}" class="text-gray-300 hover:text-emerald-400 truncate transition-colors">{{ $cp['title'] }}</a>
                                <span class="font-bold {{ $cp['percent'] === 100 ? 'text-emerald-400' : 'text-blue-400' }}">{{ $cp['percent'] }}%</span>
                            </div>
                            <div class="w-full bg-white/5 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-500 {{ $cp['percent'] === 100 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]' : 'bg-blue-500' }}" style="width:{{ $cp['percent'] }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $cp['completed'] }} / {{ $cp['total'] }} lessons</p>
                        </div>
                    @endforeach
                    <a href="{{ route('courses.index') }}" class="block text-center text-sm text-emerald-400 hover:text-emerald-300 mt-2 transition-colors">View all courses</a>
                @endif
            </div>

            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6">
                <h2 class="font-bold text-white mb-4">Puzzle Mastery</h2>
                <div class="flex justify-center mb-5">
                    <div class="relative w-32 h-32">
                        <svg class="w-32 h-32 -rotate-90" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="50" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="12"/>
                            <circle cx="60" cy="60" r="50" fill="none" stroke="{{ $puzzlePercent===100?'#10b981':'#3b82f6' }}" stroke-width="12" stroke-linecap="round" stroke-dasharray="314" stroke-dashoffset="{{ round(314*(1-$puzzlePercent/100)) }}" style="filter: drop-shadow(0 0 6px {{ $puzzlePercent===100?'rgba(16,185,129,0.5)':'rgba(59,130,246,0.5)' }})"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-extrabold text-white">{{ $puzzlePercent }}%</span>
                            <span class="text-xs text-gray-500">solved</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center mb-5">
                    <div class="bg-white/5 rounded-xl p-3">
                        <p class="text-lg font-extrabold text-emerald-400">{{ $solvedPuzzles }}</p>
                        <p class="text-xs text-gray-500">Solved</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-3">
                        <p class="text-lg font-extrabold text-gray-300">{{ $totalPuzzles }}</p>
                        <p class="text-xs text-gray-500">Total</p>
                    </div>
                    <div class="bg-white/5 rounded-xl p-3">
                        <p class="text-lg font-extrabold text-blue-400">{{ $totalAttempts }}</p>
                        <p class="text-xs text-gray-500">Attempts</p>
                    </div>
                </div>
                <a href="{{ route('puzzles.index') }}" class="block text-center px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-700 text-white text-sm font-medium rounded-xl hover:from-emerald-400 hover:to-emerald-600 transition-all duration-200 shadow-lg shadow-emerald-500/20">Go to Puzzles</a>
            </div>

            <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-6">
                <h2 class="font-bold text-white mb-4">Recent Activity</h2>
                @if($lastLesson)
                    <div class="border border-white/10 rounded-xl p-4 mb-4 bg-white/5">
                        <p class="text-xs font-semibold text-blue-400 uppercase mb-1">Last Lesson</p>
                        <p class="font-semibold text-white text-sm">{{ $lastLesson->title }}</p>
                        @if($lastLesson->course)
                            <p class="text-xs text-gray-500">{{ $lastLesson->course->title }}</p>
                        @endif
                        <a href="{{ route('courses.lesson', [$lastLesson->course, $lastLesson]) }}" class="mt-3 inline-block px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-500 transition-all duration-200">Continue</a>
                    </div>
                @else
                    <div class="border border-dashed border-white/10 rounded-xl p-4 mb-4 text-center">
                        <p class="text-gray-500 text-sm">No lesson started yet.</p>
                    </div>
                @endif

                @if($lastProGame)
                    <div class="border border-white/10 rounded-xl p-4 bg-white/5">
                        <p class="text-xs font-semibold text-purple-400 uppercase mb-1">Pro Game</p>
                        <p class="font-semibold text-white text-sm">{{ $lastProGame->title ?? 'Untitled' }}</p>
                        <a href="{{ route('pro-games.show', $lastProGame) }}" class="mt-3 inline-block px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-500 transition-all duration-200">Watch</a>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6">
            <a href="{{ route('courses.index') }}" class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-4 text-center hover:bg-white/10 text-sm font-medium text-gray-300 transition-all duration-200">Courses</a>
            <a href="{{ route('puzzles.index') }}" class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-4 text-center hover:bg-white/10 text-sm font-medium text-gray-300 transition-all duration-200">Puzzles</a>
            <a href="{{ route('pro-games.index') }}" class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-4 text-center hover:bg-white/10 text-sm font-medium text-gray-300 transition-all duration-200">Pro Games</a>
            <a href="{{ route('chat.index') }}" class="bg-white/5 backdrop-blur-md border border-white/10 rounded-xl p-4 text-center hover:bg-white/10 text-sm font-medium text-gray-300 transition-all duration-200">Chat</a>
        </div>
    </div>
</x-app-layout>
