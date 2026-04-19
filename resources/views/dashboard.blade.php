<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-sm text-slate-600 leading-tight tracking-widest uppercase dark:text-slate-500">Dashboard</h2>
    </x-slot>

    @php
    $quotes = [
        ['text' => 'Chess is life in miniature. Chess is struggle, chess is battles.', 'author' => 'Garry Kasparov'],
        ['text' => 'Every chess master was once a beginner.', 'author' => 'Irving Chernev'],
        ['text' => 'Chess is the art of analysis.', 'author' => 'Mikhail Botvinnik'],
        ['text' => 'The beauty of a move lies not in its appearance but in the thought behind it.', 'author' => 'Aron Nimzowitsch'],
        ['text' => 'Chess is not about winning, it is about creating art.', 'author' => 'Mikhail Tal'],
        ['text' => 'No price is too great for the scalp of the enemy King.', 'author' => 'Koblentz'],
        ['text' => 'Chess is a war over the board. The object is to crush the opponent\'s mind.', 'author' => 'Bobby Fischer'],
    ];
    $quote = $quotes[(int)date('N') - 1];
    @endphp

    <div class="py-8 px-4 max-w-7xl mx-auto">

        <div class="relative mb-6 overflow-hidden rounded-3xl border border-slate-200/90 p-[2px] shadow-sm dark:border-white/[0.07] dark:shadow-none" style="background: linear-gradient(135deg, rgba(239,68,68,0.12) 0%, rgba(248,250,252,0) 60%);">
            <div class="rounded-[22px] bg-white px-8 py-7 relative overflow-hidden dark:bg-[#050505]">
                <div class="absolute top-0 left-0 w-64 h-64 bg-red-500/[0.08] rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2 pointer-events-none dark:bg-red-500/[0.05]"></div>
                <div class="absolute bottom-0 right-0 w-48 h-48 bg-amber-500/[0.06] rounded-full blur-3xl pointer-events-none dark:bg-amber-500/[0.04]"></div>
                <div class="relative">
                    <p class="text-[10px] font-semibold text-red-600/70 uppercase tracking-[0.2em] mb-1.5 dark:text-red-500/60">Welcome back</p>
                    <h1 class="text-3xl font-bold text-slate-900 dark:text-white" style="font-family:'Playfair Display',serif;">{{ $user->name }}</h1>
                    <p class="text-sm text-slate-600 mt-1.5 italic dark:text-slate-500">"{{ $quote['text'] }}" <span class="not-italic text-slate-500 dark:text-slate-600">— {{ $quote['author'] }}</span></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4">

            <div class="lg:col-span-5 rounded-3xl border border-slate-200/90 bg-white p-6 shadow-sm transition-all duration-500 hover:border-slate-300 dark:bg-white/[0.03] dark:border-white/[0.07] dark:shadow-none dark:hover:border-white/[0.12] dark:hover:bg-white/[0.045]">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">My Courses</h3>
                    <a href="{{ route('courses.index') }}" class="text-[10px] font-semibold text-red-600 hover:text-red-500 uppercase tracking-widest transition-colors duration-200 dark:text-red-500/60 dark:hover:text-red-400">View all →</a>
                </div>
                @if(count($courseProgress) === 0)
                    <div class="py-10 text-center">
                        <p class="text-slate-600 text-sm mb-3 dark:text-slate-500">No courses started yet.</p>
                        <a href="{{ route('courses.index') }}" class="inline-block text-xs font-semibold text-red-600 hover:text-red-500 border border-red-200 hover:border-red-300 px-4 py-1.5 rounded-lg transition-all duration-300 dark:text-red-500/60 dark:hover:text-red-400 dark:border-red-500/20 dark:hover:border-red-500/40">Browse Courses</a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($courseProgress as $cp)
                            <div>
                                <div class="flex justify-between items-center mb-1.5">
                                    <a href="{{ $cp['url'] }}" class="text-sm text-slate-700 hover:text-slate-900 truncate transition-colors duration-200 font-medium dark:text-slate-300 dark:hover:text-white">{{ $cp['title'] }}</a>
                                    <span class="text-xs font-bold ml-3 flex-shrink-0 {{ $cp['percent'] === 100 ? 'text-amber-500 dark:text-amber-400' : 'text-blue-500 dark:text-blue-400' }}">{{ $cp['percent'] }}%</span>
                                </div>
                                <div class="w-full rounded-full h-[2px] bg-slate-200 dark:bg-white/[0.05]">
                                    <div class="h-[2px] rounded-full transition-all duration-500 {{ $cp['percent'] === 100 ? 'bg-amber-500 progress-glow dark:bg-amber-400' : 'bg-blue-500 progress-glow-blue dark:bg-blue-400' }}" style="width:{{ $cp['percent'] }}%"></div>
                                </div>
                                <p class="text-[10px] text-slate-500 mt-1 dark:text-slate-600">{{ $cp['completed'] }} / {{ $cp['total'] }} lessons</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="lg:col-span-4 flex flex-col rounded-3xl border border-slate-200/90 bg-white p-6 shadow-sm transition-all duration-500 hover:border-slate-300 dark:bg-white/[0.03] dark:border-white/[0.07] dark:shadow-none dark:hover:border-white/[0.12] dark:hover:bg-white/[0.045]">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-5">Puzzle Mastery</h3>
                <div class="flex justify-center mb-5">
                    <div class="relative w-28 h-28">
                        <svg class="w-28 h-28 -rotate-90 text-slate-200 dark:text-white/15" viewBox="0 0 120 120">
                            <circle cx="60" cy="60" r="50" fill="none" stroke="currentColor" stroke-width="8"/>
                            <circle cx="60" cy="60" r="50" fill="none" stroke-width="8" stroke-linecap="round" stroke-dasharray="314" stroke-dashoffset="{{ round(314*(1-$puzzlePercent/100)) }}"
                                stroke="{{ $puzzlePercent===100?'#f59e0b':'#3b82f6' }}"
                                style="filter: drop-shadow(0 0 6px {{ $puzzlePercent===100?'rgba(245,158,11,0.7)':'rgba(59,130,246,0.6)' }})"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-extrabold text-slate-900 dark:text-white">{{ $puzzlePercent }}%</span>
                            <span class="text-[10px] text-slate-500 uppercase tracking-wider dark:text-slate-600">solved</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center mb-5">
                    <div class="rounded-xl border border-slate-200/80 bg-slate-50 p-3 dark:bg-white/[0.03] dark:border-white/[0.06]">
                        <p class="text-base font-extrabold text-amber-600 dark:text-amber-400">{{ $solvedPuzzles }}</p>
                        <p class="text-[10px] text-slate-500 mt-0.5 uppercase tracking-wider dark:text-slate-600">Solved</p>
                    </div>
                    <div class="rounded-xl border border-slate-200/80 bg-slate-50 p-3 dark:bg-white/[0.03] dark:border-white/[0.06]">
                        <p class="text-base font-extrabold text-slate-700 dark:text-slate-300">{{ $totalPuzzles }}</p>
                        <p class="text-[10px] text-slate-500 mt-0.5 uppercase tracking-wider dark:text-slate-600">Total</p>
                    </div>
                    <div class="rounded-xl border border-slate-200/80 bg-slate-50 p-3 dark:bg-white/[0.03] dark:border-white/[0.06]">
                        <p class="text-base font-extrabold text-blue-600 dark:text-blue-400">{{ $totalAttempts }}</p>
                        <p class="text-[10px] text-slate-500 mt-0.5 uppercase tracking-wider dark:text-slate-600">Tries</p>
                    </div>
                </div>
                <a href="{{ route('puzzles.index') }}" class="mt-auto block text-center px-4 py-2.5 bg-transparent border border-red-500/50 text-red-600 text-sm font-semibold rounded-xl hover:bg-red-500 hover:text-white hover:border-red-500 hover:shadow-md transition-all duration-300 dark:border-red-500/40 dark:text-red-400 dark:hover:shadow-[0_0_20px_rgba(239,68,68,0.25)]">
                    Go to Puzzles
                </a>
            </div>

            <div class="lg:col-span-3 rounded-3xl border border-slate-200/90 bg-white p-6 shadow-sm transition-all duration-500 hover:border-slate-300 dark:bg-white/[0.03] dark:border-white/[0.07] dark:shadow-none dark:hover:border-white/[0.12] dark:hover:bg-white/[0.045]">
                <h3 class="text-base font-semibold text-slate-900 dark:text-white mb-5">Recent Activity</h3>

                @if($lastLesson)
                    <div class="rounded-2xl border border-blue-200/80 bg-blue-50/50 p-4 mb-3 dark:border-blue-500/15 dark:bg-blue-500/[0.03]">
                        <p class="text-[10px] font-semibold text-blue-600 uppercase tracking-[0.15em] mb-1.5 dark:text-blue-400/70">Last Lesson</p>
                        <p class="font-semibold text-slate-900 text-sm leading-snug dark:text-white">{{ $lastLesson->title }}</p>
                        @if($lastLesson->course)
                            <p class="text-[11px] text-slate-500 mt-0.5 dark:text-slate-600">{{ $lastLesson->course->title }}</p>
                        @endif
                        <a href="{{ route('courses.lesson', [$lastLesson->course, $lastLesson]) }}"
                           class="mt-3 inline-block px-3 py-1.5 bg-transparent border border-blue-400/40 text-blue-600 text-xs font-semibold rounded-lg hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-300 dark:border-blue-500/30 dark:text-blue-400">
                            Continue →
                        </a>
                    </div>
                @else
                    <div class="rounded-2xl border border-dashed border-slate-200 p-4 mb-3 text-center dark:border-white/[0.07]">
                        <p class="text-slate-500 text-xs dark:text-slate-600">No lesson started yet.</p>
                    </div>
                @endif

                @if($lastProGame)
                    <div class="rounded-2xl border border-amber-200/80 bg-amber-50/50 p-4 dark:border-amber-500/15 dark:bg-amber-500/[0.03]">
                        <p class="text-[10px] font-semibold text-amber-700 uppercase tracking-[0.15em] mb-1.5 dark:text-amber-400/70">Pro Game</p>
                        <p class="font-semibold text-slate-900 text-sm leading-snug dark:text-white">{{ $lastProGame->title ?? 'Untitled' }}</p>
                        <a href="{{ route('pro-games.show', $lastProGame) }}"
                           class="mt-3 inline-block px-3 py-1.5 bg-transparent border border-amber-500/40 text-amber-700 text-xs font-semibold rounded-lg hover:bg-amber-500 hover:text-white hover:border-amber-500 transition-all duration-300 dark:text-amber-400 dark:border-amber-500/30">
                            Watch →
                        </a>
                    </div>
                @endif
            </div>

        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4">
            @foreach([
                ['route' => 'courses.index',   'label' => 'Courses',    'char' => '♟'],
                ['route' => 'puzzles.index',   'label' => 'Puzzles',    'char' => '♜'],
                ['route' => 'pro-games.index', 'label' => 'Pro Games',  'char' => '♛'],
                ['route' => 'chat.index',      'label' => 'Chat',       'char' => '♞'],
            ] as $link)
            <a href="{{ route($link['route']) }}"
               class="group flex items-center gap-3 rounded-2xl border border-slate-200/90 bg-white px-4 py-3 shadow-sm transition-all duration-300 hover:border-red-300 hover:bg-slate-50 dark:bg-white/[0.025] dark:border-white/[0.06] dark:shadow-none dark:hover:bg-white/[0.05] dark:hover:border-red-500/20">
                <span class="text-xl text-slate-400 transition-colors duration-300 group-hover:text-red-500 dark:text-slate-600 dark:group-hover:text-red-400/70 leading-none select-none">{{ $link['char'] }}</span>
                <span class="text-sm font-medium text-slate-600 transition-colors duration-300 group-hover:text-slate-900 dark:text-slate-500 dark:group-hover:text-slate-200">{{ $link['label'] }}</span>
            </a>
            @endforeach
        </div>

    </div>
</x-app-layout>
