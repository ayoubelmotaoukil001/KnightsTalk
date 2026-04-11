@push('styles')
    <link rel="stylesheet" href="{{ asset('css/chessboard.css') }}">
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">New puzzle</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-6xl mx-auto">
        <p class="mb-4">
            <a href="{{ route('admin.puzzles.index') }}" class="text-blue-600 underline">Back to list</a>
        </p>

        <form id="puzzle-form" method="post" action="{{ route('admin.puzzles.store') }}">
            @csrf

            <input type="hidden" name="initial_fen" id="initial_fen" value="{{ old('initial_fen', '') }}">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                {{-- Board side --}}
                <div>
                    <p class="font-medium text-gray-800 mb-2">Board (drag pieces or use spares)</p>
                    <div id="board" class="w-full max-w-[480px]"></div>
                    <button type="button" id="clear-board"
                        class="mt-3 px-3 py-1 border border-gray-400 rounded bg-white hover:bg-gray-50 text-sm">
                        Clear board
                    </button>
                </div>

                {{-- Form side --}}
                <div class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                            class="mt-1 block w-full border border-gray-300 rounded px-2 py-1">
                        @error('title')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-700">Difficulty</label>
                        <select id="difficulty" name="difficulty" required
                            class="mt-1 block w-full border border-gray-300 rounded px-2 py-1">
                            <option value="">Pick one</option>
                            <option value="easy" @selected(old('difficulty') == 'easy')>easy</option>
                            <option value="medium" @selected(old('difficulty') == 'medium')>medium</option>
                            <option value="hard" @selected(old('difficulty') == 'hard')>hard</option>
                        </select>
                        @error('difficulty')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="solution_text" class="block text-sm font-medium text-gray-700">Solution moves</label>
                        <p class="text-xs text-gray-500 mt-1">One move per line (typed by you). On save, each line becomes one entry in <code>solution[]</code>.</p>
                        @php
                            $oldSolution = old('solution');
                            $solutionText = is_array($oldSolution) ? implode("\n", $oldSolution) : '';
                        @endphp
                        <textarea id="solution_text" rows="12"
                            class="mt-1 block w-full border border-gray-300 rounded px-2 py-1 font-mono text-sm">{{ $solutionText }}</textarea>
                        @error('solution')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button type="submit"
                            class="px-4 py-2 bg-gray-800 text-white rounded text-sm hover:bg-gray-700">
                            Save puzzle
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/chessboard.js') }}"></script>
        <script>
            (function () {
                var fenInput = document.getElementById('initial_fen');
                var form = document.getElementById('puzzle-form');
                var ta = document.getElementById('solution_text');
                if (!fenInput || !form || !ta) return;

                var savedFen = (fenInput.value || '').trim();
                var startPos = savedFen.length ? savedFen : 'start';

                var board = Chessboard('board', {
                    position: startPos,
                    draggable: true,
                    sparePieces: true,
                    pieceTheme: @json(asset('img/chesspieces/wikipedia').'/{piece}.png'),
                    onChange: function () {
                        fenInput.value = board.fen();
                    }
                });

                fenInput.value = board.fen();

                document.getElementById('clear-board').addEventListener('click', function () {
                    board.clear();
                    fenInput.value = board.fen();
                });

                form.addEventListener('submit', function () {
                    fenInput.value = board.fen();

                    form.querySelectorAll('input[name^="solution["]').forEach(function (el) {
                        el.remove();
                    });

                    var lines = ta.value.split(/\r?\n/).map(function (l) {
                        return l.trim();
                    }).filter(function (l) {
                        return l.length > 0;
                    });

                    lines.forEach(function (move, i) {
                        var inp = document.createElement('input');
                        inp.type = 'hidden';
                        inp.name = 'solution[' + i + ']';
                        inp.value = move;
                        form.appendChild(inp);
                    });
                });
            })();
        </script>
    @endpush
</x-app-layout>
