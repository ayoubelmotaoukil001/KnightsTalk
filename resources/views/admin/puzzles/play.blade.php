@push('styles')
    <link rel="stylesheet" href="{{ asset('css/chessboard.css') }}">
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">Solve: {{ $puzzle->title }}</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-6xl mx-auto">
        <p class="mb-4">
            <a href="{{ route('admin.puzzles.index') }}" class="text-emerald-400 hover:text-emerald-300 transition-colors">Back to list</a>
            <span class="mx-2 text-gray-600">|</span>
            <a href="{{ route('admin.puzzles.edit', $puzzle) }}" class="text-emerald-400 hover:text-emerald-300 transition-colors">Edit puzzle</a>
        </p>

        <p class="text-sm text-gray-400 mb-2">Difficulty: <strong class="text-gray-200">{{ $puzzle->difficulty }}</strong></p>
        <p id="step-label" class="text-sm font-medium text-gray-300 mb-4"></p>

        <div id="feedback" class="mb-4 text-sm font-medium hidden"></div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            <div>
                <p class="font-medium text-gray-300 mb-2">Board</p>
                <div id="board" class="w-full max-w-[480px] rounded-xl overflow-hidden shadow-2xl shadow-black/50 ring-1 ring-white/10"></div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <button type="button" id="btn-step-back"
                        class="px-3 py-1.5 border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 text-sm text-gray-300 transition-all duration-200">
                        Step back
                    </button>
                    <button type="button" id="btn-reset"
                        class="px-3 py-1.5 border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 text-sm text-gray-300 transition-all duration-200">
                        Start over
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="move_input" class="block text-sm font-medium text-gray-300">Type your move</label>
                    <p class="text-xs text-gray-500 mt-1">Type only <strong class="text-gray-400">your</strong> moves. After each correct move, the site plays the next move from the puzzle line (the other side). All of them show in the list below. Wrong move: try again. <strong class="text-gray-400">Step back</strong> takes back your last move and the reply after it when there was one. When you build the puzzle, list the moves in order: your side, then theirs, then yours, and so on, starting with whoever is to move on the diagram. For typing, <code class="text-emerald-400">nf3</code> and <code class="text-emerald-400">Nf3</code> are treated the same; castling can be <code class="text-emerald-400">O-O</code> or <code class="text-emerald-400">0-0</code>.</p>
                    <div class="mt-2 flex flex-wrap gap-2 items-center">
                        <input type="text" id="move_input" autocomplete="off"
                            class="bg-white/5 border border-white/10 text-white rounded-xl px-3 py-2 font-mono text-sm w-48 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 placeholder-gray-500 transition-all duration-200"
                            placeholder="e.g. e4">
                        <button type="button" id="btn-submit-move"
                            class="px-4 py-2 bg-gradient-to-r from-emerald-500 to-emerald-700 text-white rounded-xl text-sm hover:from-emerald-400 hover:to-emerald-600 font-medium transition-all duration-200 shadow-lg shadow-emerald-500/20">
                            Submit move
                        </button>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-300">Moves so far</p>
                    <p id="played-moves" class="mt-1 text-sm font-mono text-gray-400">&mdash;</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/chess.js') }}"></script>
        <script src="{{ asset('js/chessboard.js') }}"></script>
        <script>
            (function () {
                var startFen = @json($puzzle->initial_fen);
                var solution = @json($puzzle->solution ?? []);

                function makeFullFen(savedFen) {
                    savedFen = (savedFen || '').trim();
                    if (savedFen === '') {
                        return 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';
                    }
                    var parts = savedFen.split(/\s+/);
                    if (parts.length >= 6) {
                        return savedFen;
                    }
                    return parts[0] + ' w - - 0 1';
                }

                var game = Chess();
                if (!game.load(makeFullFen(startFen))) {
                    game.reset();
                }

                var moveSpeedMs = 300;
                var pendingAfterAnim = null;

                function onBoardMoveEnd() {
                    if (!pendingAfterAnim) return;
                    var fn = pendingAfterAnim.fn;
                    pendingAfterAnim = null;
                    fn();
                }

                var board = Chessboard('board', {
                    position: game.fen().split(' ')[0],
                    draggable: false,
                    pieceTheme: @json(asset('img/chesspieces/wikipedia').'/{piece}.png'),
                    moveSpeed: moveSpeedMs,
                    onMoveEnd: onBoardMoveEnd,
                });

                var msg = document.getElementById('feedback');
                var stepText = document.getElementById('step-label');
                var input = document.getElementById('move_input');
                var list = document.getElementById('played-moves');
                var btnGo = document.getElementById('btn-submit-move');

                var replyTimer = null;
                var replyDelayMs = 750;

                function clearReplyTimer() {
                    if (replyTimer !== null) { clearTimeout(replyTimer); replyTimer = null; }
                }
                function clearAnimWait() { pendingAfterAnim = null; }

                function showBoardAnimated(piecesFen, then) {
                    var token = { fn: then };
                    pendingAfterAnim = token;
                    board.position(piecesFen, true);
                    setTimeout(function () {
                        if (pendingAfterAnim !== token) return;
                        pendingAfterAnim = null;
                        board.position(piecesFen, false);
                        then();
                    }, moveSpeedMs * 3 + 250);
                }

                function howManyMovesDone() { return game.history().length; }

                function lastPlyWasAutoReply() {
                    var h = howManyMovesDone();
                    return h > 0 && h % 2 === 0;
                }

                function drawBoard() {
                    clearAnimWait();
                    board.position(game.fen().split(' ')[0], false);
                }

                function setMessage(ok, text) {
                    msg.classList.remove('hidden');
                    msg.style.color = ok ? '#10b981' : '#f87171';
                    msg.textContent = text;
                }

                function clearMessage() {
                    msg.classList.add('hidden');
                    msg.textContent = '';
                }

                function refreshLabels() {
                    if (solution.length === 0) {
                        stepText.textContent = 'No solution moves in the database for this puzzle.';
                        return;
                    }
                    var n = howManyMovesDone();
                    if (n >= solution.length) {
                        stepText.textContent = 'Puzzle complete.';
                    } else {
                        stepText.textContent = 'Move ' + (n + 1) + ' of ' + solution.length;
                    }
                    var past = game.history();
                    if (past.length === 0) {
                        list.textContent = '—';
                    } else {
                        list.textContent = past.join(', ');
                    }
                }

                function sameMove(a, b) {
                    var x = (a || '').trim().replace(/\+|#/g, '').toLowerCase();
                    var y = (b || '').trim().replace(/\+|#/g, '').toLowerCase();
                    return x === y;
                }

                function normalizeMoveInput(raw) {
                    var s = (raw || '').trim();
                    if (s === '') return s;
                    var castle = s.replace(/0/g, 'O').replace(/\s/g, '');
                    if (/^o-o-o$/i.test(castle)) return 'O-O-O';
                    if (/^o-o$/i.test(castle)) return 'O-O';
                    var c = s.charAt(0);
                    if (c === 'n' || c === 'r' || c === 'q' || c === 'k') return c.toUpperCase() + s.slice(1);
                    if (c === 'b') {
                        if (/^bx[a-h][1-8]/.test(s)) return s;
                        if (/^b[1-8]/.test(s)) return s;
                        return 'B' + s.slice(1);
                    }
                    if (/=[qrbn]$/i.test(s)) return s.slice(0, -1) + s.charAt(s.length - 1).toUpperCase();
                    return s;
                }

                function onSubmit() {
                    clearMessage(); clearReplyTimer(); clearAnimWait();
                    if (solution.length === 0) { setMessage(false, 'No solution is stored for this puzzle.'); return; }
                    if (howManyMovesDone() >= solution.length) { setMessage(true, 'You already finished this puzzle.'); return; }
                    var typed = normalizeMoveInput(input.value);
                    if (typed === '') { setMessage(false, 'Type a move first.'); return; }
                    var want = solution[howManyMovesDone()];
                    var played = game.move(typed, { sloppy: true });
                    if (played === null) { setMessage(false, 'That move is not allowed here. Try again.'); return; }
                    if (!sameMove(played.san, want)) { game.undo(); setMessage(false, 'Not the right move for this step. Try again.'); return; }

                    var piecesAfterUser = game.fen().split(' ')[0];
                    input.value = '';
                    input.disabled = true;
                    btnGo.disabled = true;

                    function afterUserMoveShown() {
                        refreshLabels();
                        if (howManyMovesDone() < solution.length) {
                            var replySan = solution[howManyMovesDone()];
                            setMessage(true, 'Good — give them a moment…');
                            replyTimer = setTimeout(function () {
                                replyTimer = null;
                                var reply = game.move(replySan, { sloppy: true });
                                if (reply === null) {
                                    game.undo(); drawBoard(); input.disabled = false; btnGo.disabled = false;
                                    setMessage(false, 'Puzzle setup error: the next line move cannot be played: ' + replySan);
                                    refreshLabels(); return;
                                }
                                var piecesAfterReply = game.fen().split(' ')[0];
                                showBoardAnimated(piecesAfterReply, function () {
                                    refreshLabels();
                                    if (howManyMovesDone() >= solution.length) {
                                        setMessage(true, 'You solved it!'); input.disabled = true; btnGo.disabled = true;
                                    } else {
                                        setMessage(true, 'They play ' + reply.san + '. Your turn.');
                                        input.disabled = false; btnGo.disabled = false; input.focus();
                                    }
                                });
                            }, replyDelayMs);
                            return;
                        }
                        if (howManyMovesDone() >= solution.length) {
                            setMessage(true, 'You solved it!');
                        } else {
                            setMessage(true, 'Good — next move.');
                            input.disabled = false; btnGo.disabled = false; input.focus();
                        }
                    }
                    showBoardAnimated(piecesAfterUser, afterUserMoveShown);
                }

                btnGo.addEventListener('click', onSubmit);
                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') { e.preventDefault(); onSubmit(); }
                });

                document.getElementById('btn-step-back').addEventListener('click', function () {
                    clearReplyTimer(); clearAnimWait(); clearMessage();
                    if (game.history().length === 0) { setMessage(false, 'Nothing to undo yet.'); return; }
                    if (lastPlyWasAutoReply()) game.undo();
                    if (game.history().length > 0) game.undo();
                    drawBoard(); input.disabled = false; btnGo.disabled = false; refreshLabels(); input.focus();
                });

                document.getElementById('btn-reset').addEventListener('click', function () {
                    clearReplyTimer(); clearAnimWait();
                    game.load(makeFullFen(startFen));
                    drawBoard(); input.value = ''; input.disabled = false; btnGo.disabled = false;
                    clearMessage(); refreshLabels(); input.focus();
                });

                refreshLabels(); input.focus();
            })();
        </script>
    @endpush
</x-app-layout>
