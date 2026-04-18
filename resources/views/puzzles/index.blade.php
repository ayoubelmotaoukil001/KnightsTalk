<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">Puzzles</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4">

        @if($total > 0)
        <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-5 mb-6">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <p class="text-lg font-bold text-white">Your Progress</p>
                    <p class="text-sm text-gray-400 mt-0.5">
                        Total Puzzles Solved:
                        <span class="font-semibold text-gray-200">{{ $solved }} / {{ $total }}</span>
                    </p>
                </div>
                @if($percent === 100)
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-500/20 text-emerald-400 text-sm font-bold rounded-full border border-emerald-500/30">
                        All Solved!
                    </span>
                @else
                    <span class="text-2xl font-bold text-blue-400">{{ $percent }}%</span>
                @endif
            </div>

            <div class="w-full bg-white/5 rounded-full h-3">
                <div class="h-3 rounded-full transition-all duration-700
                    {{ $percent === 100 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.4)]' : 'bg-blue-500' }}"
                     style="width: {{ $percent }}%">
                </div>
            </div>
        </div>
        @endif

        @if ($puzzles->isEmpty())
            <p class="text-gray-500">No puzzles yet. Check back later!</p>
        @else
            <div class="space-y-3">
                @foreach ($puzzles as $puzzle)
                    @php $solved_this = in_array($puzzle->id, $completedIds); @endphp
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 flex items-center justify-between
                        {{ $solved_this ? 'border-l-4 border-l-emerald-500' : '' }} transition-all duration-200">
                        <div class="flex items-center gap-3">
                            @if($solved_this)
                                <span class="flex-shrink-0 w-9 h-9 bg-emerald-500 text-white rounded-full flex items-center justify-center font-bold text-base shadow-lg shadow-emerald-500/30">
                                    &#x2713;
                                </span>
                            @else
                                <span class="flex-shrink-0 w-9 h-9 bg-white/10 text-gray-400 rounded-full flex items-center justify-center font-bold text-sm">
                                    {{ $loop->iteration }}
                                </span>
                            @endif

                            <div>
                                <p class="font-semibold text-white">{{ $puzzle->title }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-500">{{ $puzzle->difficulty }}</span>
                                    @if($solved_this)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-xs font-semibold rounded-full">
                                            Solved
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('puzzles.play', $puzzle) }}"
                           class="{{ $solved_this
                               ? 'bg-white/5 text-gray-400 hover:bg-white/10 border border-white/10'
                               : 'bg-gradient-to-r from-emerald-500 to-emerald-700 text-white hover:from-emerald-400 hover:to-emerald-600 shadow-lg shadow-emerald-500/20' }}
                               text-sm px-4 py-2 rounded-xl font-medium transition-all duration-200">
                            {{ $solved_this ? 'Retry' : 'Solve' }}
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</x-app-layout>
