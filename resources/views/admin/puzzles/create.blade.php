@push('styles')
    <link rel="stylesheet" href="{{ asset('css/chessboard.css') }}">
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">New puzzle</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-6xl mx-auto">
        <p class="mb-4">
            <a href="{{ route('admin.puzzles.index') }}" class="text-emerald-400 hover:text-emerald-300 transition-colors">&larr; Back to list</a>
        </p>

        <form id="puzzle-form" method="post" action="{{ route('admin.puzzles.store') }}">
            @csrf

            <input type="hidden" name="initial_fen" id="initial_fen" value="{{ old('initial_fen', '') }}">

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                <div>
                    <p class="font-medium text-gray-300 mb-2">Board (drag pieces or use spares)</p>
                    <div id="board" class="w-full max-w-[480px] rounded-xl overflow-hidden shadow-2xl shadow-black/50 ring-1 ring-white/10"></div>
                    <button type="button" id="clear-board"
                        class="mt-3 px-3 py-1.5 border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 text-sm text-gray-300 transition-all duration-200">
                        Clear board
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-300 mb-1">Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                            class="w-full bg-white/5 border border-white/10 text-white rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 placeholder-gray-500 transition-all duration-200">
                        @error('title')
                            <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="difficulty" class="block text-sm font-medium text-gray-300 mb-1">Difficulty</label>
                        <select id="difficulty" name="difficulty" required
                            class="w-full bg-white/5 border border-white/10 text-white rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all duration-200">
                            <option value="">Pick one</option>
                            <option value="easy" @selected(old('difficulty') == 'easy')>easy</option>
                            <option value="medium" @selected(old('difficulty') == 'medium')>medium</option>
                            <option value="hard" @selected(old('difficulty') == 'hard')>hard</option>
                        </select>
                        @error('difficulty')
                            <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Turn Toggle --}}
                    <div class="flex items-center gap-4 p-3 bg-white/5 border border-white/10 rounded-xl">
                        <span class="text-sm font-medium text-gray-300">Who moves first?</span>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <span id="turn-label" class="text-sm font-semibold text-white">White</span>
                            <div class="relative">
                                <input type="checkbox" id="turn-toggle" class="sr-only">
                                <div class="w-11 h-6 bg-white/20 rounded-full peer-checked:bg-emerald-600 transition-colors duration-200" id="toggle-track"></div>
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform duration-200" id="toggle-thumb"></div>
                            </div>
                            <span class="text-sm text-gray-400">Black</span>
                        </label>
                    </div>

                    {{-- Validate Button + Feedback --}}
                    <div class="space-y-2">
                        <button type="button" id="validate-btn"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-sm font-medium transition-all duration-200">
                            Validate Position
                        </button>
                        <div id="validate-msg" class="hidden text-sm font-medium rounded-lg px-3 py-2"></div>
                    </div>

                    <div>
                        <label for="solution_text" class="block text-sm font-medium text-gray-300 mb-1">Solution moves</label>
                        <p class="text-xs text-gray-500 mt-1">One move per line. On save, each line becomes one entry in <code class="text-emerald-400">solution[]</code>.</p>
                        @php
                            $oldSolution = old('solution');
                            $solutionText = is_array($oldSolution) ? implode("\n", $oldSolution) : '';
                        @endphp
                        <textarea id="solution_text" rows="12"
                            class="mt-1 w-full bg-white/5 border border-white/10 text-white rounded-xl px-4 py-3 font-mono text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 placeholder-gray-500 transition-all duration-200">{{ $solutionText }}</textarea>
                        @error('solution')
                            <p class="text-sm text-red-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <button type="submit" id="save-btn"
                            class="px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-700 text-white rounded-xl hover:from-emerald-400 hover:to-emerald-600 text-sm font-medium transition-all duration-200 shadow-lg shadow-emerald-500/20">
                            Save puzzle
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/chess.js') }}"></script>
        <script src="{{ asset('js/chessboard.js') }}"></script>
        <script>
            (function () {
                var fenInput  = document.getElementById('initial_fen');
                var form      = document.getElementById('puzzle-form');
                var ta        = document.getElementById('solution_text');
                var saveBtn   = document.getElementById('save-btn');
                var valBtn    = document.getElementById('validate-btn');
                var valMsg    = document.getElementById('validate-msg');
                var toggle    = document.getElementById('turn-toggle');
                var thumb     = document.getElementById('toggle-thumb');
                var track     = document.getElementById('toggle-track');
                var turnLabel = document.getElementById('turn-label');
                if (!fenInput || !form || !ta) return;

                var currentTurn = 'w'; // default White
                var positionValid = false;

                var savedFen = (fenInput.value || '').trim();
                var startPos = savedFen.length ? savedFen : 'start';

                var board = Chessboard('board', {
                    position: startPos,
                    draggable: true,
                    sparePieces: true,
                    pieceTheme: @json(asset('img/chesspieces/wikipedia').'/{piece}.png'),
                    onChange: function () {
                        fenInput.value = buildFen();
                        positionValid = false;
                        showMsg('', '');
                    }
                });

                fenInput.value = buildFen();

                // Build full FEN with current turn
                function buildFen() {
                    var boardFen = board.fen();
                    if (!boardFen) return '';
                    var position = boardFen.split(' ')[0]; // piece placement only
                    return position + ' ' + currentTurn + ' - - 0 1';
                }

                // Turn toggle
                toggle.addEventListener('change', function () {
                    if (toggle.checked) {
                        currentTurn = 'b';
                        turnLabel.textContent = 'Black';
                        thumb.style.transform = 'translateX(20px)';
                        track.style.backgroundColor = '#10b981';
                    } else {
                        currentTurn = 'w';
                        turnLabel.textContent = 'White';
                        thumb.style.transform = 'translateX(0)';
                        track.style.backgroundColor = 'rgba(255,255,255,0.2)';
                    }
                    fenInput.value = buildFen();
                    positionValid = false;
                    showMsg('', '');
                });

                // Show message helper
                function showMsg(text, type) {
                    if (!text) { valMsg.classList.add('hidden'); return; }
                    valMsg.classList.remove('hidden');
                    if (type === 'error') {
                        valMsg.className = 'text-sm font-medium rounded-lg px-3 py-2 bg-red-500/20 text-red-400 border border-red-500/30';
                    } else {
                        valMsg.className = 'text-sm font-medium rounded-lg px-3 py-2 bg-emerald-500/20 text-emerald-400 border border-emerald-500/30';
                    }
                    valMsg.textContent = text;
                }

                // Validate button
                valBtn.addEventListener('click', function () {
                    var fen = buildFen();
                    fenInput.value = fen;

                    var chess = new Chess();

                    // Step 1 – can chess.js even load this FEN?
                    if (!chess.load(fen)) {
                        positionValid = false;
                        saveBtn.disabled = true;
                        showMsg('Illegal Position: FEN is invalid or impossible.', 'error');
                        return;
                    }

                    // Step 2 – count kings
                    var board8 = chess.board();
                    var whiteKings = 0, blackKings = 0;
                    for (var r = 0; r < 8; r++) {
                        for (var c = 0; c < 8; c++) {
                            var sq = board8[r][c];
                            if (sq && sq.type === 'k') {
                                if (sq.color === 'w') whiteKings++;
                                else blackKings++;
                            }
                        }
                    }
                    if (whiteKings !== 1) {
                        positionValid = false;
                        saveBtn.disabled = true;
                        showMsg('Illegal Position: White must have exactly one king (found ' + whiteKings + ').', 'error');
                        return;
                    }
                    if (blackKings !== 1) {
                        positionValid = false;
                        saveBtn.disabled = true;
                        showMsg('Illegal Position: Black must have exactly one king (found ' + blackKings + ').', 'error');
                        return;
                    }

                    // Step 3 – pawns on rank 1 or rank 8
                    var ranks = fen.split(' ')[0].split('/');
                    var firstRank = ranks[7]; // rank 1
                    var eighthRank = ranks[0]; // rank 8
                    if (/[pP]/.test(firstRank) || /[pP]/.test(eighthRank)) {
                        positionValid = false;
                        saveBtn.disabled = true;
                        showMsg('Illegal Position: Pawns cannot be on the 1st or 8th rank.', 'error');
                        return;
                    }

                    // Step 4 – side NOT to move must not be in check
                    var notToMove = (currentTurn === 'w') ? 'b' : 'w';
                    var flipFen = fen.replace(' ' + currentTurn + ' ', ' ' + notToMove + ' ');
                    var chessFlip = new Chess();
                    if (chessFlip.load(flipFen) && chessFlip.in_check()) {
                        positionValid = false;
                        saveBtn.disabled = true;
                        var sideName = (notToMove === 'w') ? 'White' : 'Black';
                        showMsg('Illegal Position: ' + sideName + ' is in check but it is not their turn.', 'error');
                        return;
                    }

                    // All good!
                    positionValid = true;
                    saveBtn.disabled = false;
                    showMsg('Position is Legal', 'ok');
                });

                document.getElementById('clear-board').addEventListener('click', function () {
                    board.clear();
                    fenInput.value = buildFen();
                    positionValid = false;
                    showMsg('', '');
                });

                form.addEventListener('submit', function () {
                    fenInput.value = buildFen();
                    form.querySelectorAll('input[name^="solution["]').forEach(function (el) { el.remove(); });
                    var lines = ta.value.split(/\r?\n/).map(function (l) { return l.trim(); }).filter(function (l) { return l.length > 0; });
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
