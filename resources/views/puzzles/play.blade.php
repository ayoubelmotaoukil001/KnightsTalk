@push('styles')
    <link rel="stylesheet" href="{{ asset('css/chessboard.css') }}">
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Solve: {{ $puzzle->title }}</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-6xl mx-auto">

        <p class="mb-4">
            <a href="{{ route('puzzles.index') }}" class="text-blue-600 underline">Back to puzzles</a>
        </p>

        <p class="text-sm text-gray-600 mb-2">Difficulty: <strong>{{ $puzzle->difficulty }}</strong></p>
        <p id="step-label" class="text-sm font-medium text-gray-800 mb-4"></p>

        <div id="feedback" class="mb-4 text-sm font-medium hidden"></div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            <div>
                <p class="font-medium text-gray-800 mb-2">Board</p>
                <div id="board" class="w-full max-w-[480px]"></div>
                <div class="mt-3 flex flex-wrap gap-2">
                    <button type="button" id="btn-step-back"
                        class="px-3 py-1 border border-gray-400 rounded bg-white hover:bg-gray-50 text-sm">
                        Step back
                    </button>
                    <button type="button" id="btn-reset"
                        class="px-3 py-1 border border-gray-400 rounded bg-white hover:bg-gray-50 text-sm">
                        Start over
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label for="move_input" class="block text-sm font-medium text-gray-700">Type your move</label>
                    <p class="text-xs text-gray-500 mt-1">Type only <strong>your</strong> moves. After each correct move, the site plays the next move from the puzzle line. Wrong move: try again. <strong>Step back</strong> undoes your last move.</p>
                    <div class="mt-2 flex flex-wrap gap-2 items-center">
                        <input type="text" id="move_input" autocomplete="off"
                            class="border border-gray-300 rounded px-2 py-1 font-mono text-sm w-48"
                            placeholder="e.g. e4">
                        <button type="button" id="btn-submit-move"
                            class="px-4 py-2 bg-gray-800 text-white rounded text-sm hover:bg-gray-700">
                            Submit move
                        </button>
                    </div>
                </div>

                <div>
                    <p class="text-sm font-medium text-gray-700">Moves so far</p>
                    <p id="played-moves" class="mt-1 text-sm font-mono text-gray-600">—</p>
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
                    if (savedFen === '') return 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';
                    var parts = savedFen.split(/\s+/);
                    if (parts.length >= 6) return savedFen;
                    return parts[0] + ' w - - 0 1';
                }

                var game = Chess();
                if (!game.load(makeFullFen(startFen))) game.reset();

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

                var msg      = document.getElementById('feedback');
                var stepText = document.getElementById('step-label');
                var input    = document.getElementById('move_input');
                var list     = document.getElementById('played-moves');
                var btnGo    = document.getElementById('btn-submit-move');
                var replyTimer = null;

                function clearReplyTimer() {
                    if (replyTimer !== null) { clearTimeout(replyTimer); replyTimer = null; }
                }

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

                function drawBoard() {
                    pendingAfterAnim = null;
                    board.position(game.fen().split(' ')[0], false);
                }

                function setMessage(ok, text) {
                    msg.classList.remove('hidden');
                    msg.style.color = ok ? 'green' : 'red';
                    msg.textContent = text;
                }

                function clearMessage() { msg.classList.add('hidden'); msg.textContent = ''; }

                function refreshLabels() {
                    if (solution.length === 0) { stepText.textContent = 'No solution moves stored.'; return; }
                    var n = game.history().length;
                    stepText.textContent = n >= solution.length ? 'Puzzle complete.' : 'Move ' + (n + 1) + ' of ' + solution.length;
                    list.textContent = game.history().length === 0 ? '—' : game.history().join(', ');
                }

                function sameMove(a, b) {
                    return (a || '').trim().replace(/\+|#/g, '').toLowerCase() === (b || '').trim().replace(/\+|#/g, '').toLowerCase();
                }

                function normalizeMoveInput(raw) {
                    var s = (raw || '').trim();
                    if (s === '') return s;
                    var castle = s.replace(/0/g, 'O').replace(/\s/g, '');
                    if (/^o-o-o$/i.test(castle)) return 'O-O-O';
                    if (/^o-o$/i.test(castle)) return 'O-O';
                    var c = s.charAt(0);
                    if ('nrqk'.includes(c)) return c.toUpperCase() + s.slice(1);
                    if (c === 'b') {
                        if (/^bx[a-h][1-8]/.test(s) || /^b[1-8]/.test(s)) return s;
                        return 'B' + s.slice(1);
                    }
                    if (/=[qrbn]$/i.test(s)) return s.slice(0, -1) + s.charAt(s.length - 1).toUpperCase();
                    return s;
                }

                function onSubmit() {
                    clearMessage();
                    clearReplyTimer();
                    pendingAfterAnim = null;

                    if (solution.length === 0) { setMessage(false, 'No solution stored for this puzzle.'); return; }
                    if (game.history().length >= solution.length) { setMessage(true, 'You already finished this puzzle.'); return; }

                    var typed = normalizeMoveInput(input.value);
                    if (typed === '') { setMessage(false, 'Type a move first.'); return; }

                    var want   = solution[game.history().length];
                    var played = game.move(typed, { sloppy: true });

                    if (played === null) { setMessage(false, 'That move is not allowed. Try again.'); return; }
                    if (!sameMove(played.san, want)) { game.undo(); setMessage(false, 'Not the right move. Try again.'); return; }

                    var piecesAfterUser = game.fen().split(' ')[0];
                    input.value = '';
                    input.disabled = true;
                    btnGo.disabled = true;

                    showBoardAnimated(piecesAfterUser, function () {
                        refreshLabels();
                        if (game.history().length >= solution.length) {
                            setMessage(true, 'You solved it!');
                            return;
                        }
                        var replySan = solution[game.history().length];
                        setMessage(true, 'Good — give them a moment…');
                        replyTimer = setTimeout(function () {
                            replyTimer = null;
                            var reply = game.move(replySan, { sloppy: true });
                            if (reply === null) {
                                drawBoard();
                                input.disabled = false;
                                btnGo.disabled = false;
                                setMessage(false, 'Puzzle error: next move is invalid: ' + replySan);
                                refreshLabels();
                                return;
                            }
                            showBoardAnimated(game.fen().split(' ')[0], function () {
                                refreshLabels();
                                if (game.history().length >= solution.length) {
                                    setMessage(true, 'You solved it!');
                                } else {
                                    setMessage(true, 'They play ' + reply.san + '. Your turn.');
                                    input.disabled = false;
                                    btnGo.disabled = false;
                                    input.focus();
                                }
                            });
                        }, 750);
                    });
                }

                btnGo.addEventListener('click', onSubmit);
                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') { e.preventDefault(); onSubmit(); }
                });

                document.getElementById('btn-step-back').addEventListener('click', function () {
                    clearReplyTimer();
                    pendingAfterAnim = null;
                    clearMessage();
                    if (game.history().length === 0) { setMessage(false, 'Nothing to undo yet.'); return; }
                    if (game.history().length % 2 === 0) game.undo();
                    if (game.history().length > 0) game.undo();
                    drawBoard();
                    input.disabled = false;
                    btnGo.disabled = false;
                    refreshLabels();
                    input.focus();
                });

                document.getElementById('btn-reset').addEventListener('click', function () {
                    clearReplyTimer();
                    pendingAfterAnim = null;
                    game.load(makeFullFen(startFen));
                    drawBoard();
                    input.value = '';
                    input.disabled = false;
                    btnGo.disabled = false;
                    clearMessage();
                    refreshLabels();
                    input.focus();
                });

                refreshLabels();
                input.focus();
            })();
        </script>
    @endpush
</x-app-layout>
