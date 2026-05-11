@push('styles')
    <link rel="stylesheet" href="{{ asset('css/chessboard.css') }}">
    <style>
        #board-wrap {
            transition: box-shadow 0.5s ease;
            border-radius: 12px;
            overflow: hidden;
        }
        #board-wrap.player-turn {
            box-shadow: 0 0 0 1px rgba(239,68,68,0.3), 0 0 40px rgba(239,68,68,0.2), 0 0 80px rgba(239,68,68,0.08);
        }
        #board-wrap.opponent-turn {
            box-shadow: 0 0 0 1px rgba(255,255,255,0.06), 0 0 20px rgba(0,0,0,0.5);
        }
        html:not(.dark) #board-wrap.opponent-turn {
            box-shadow: 0 0 0 1px rgba(15,23,42,0.08), 0 4px 24px rgba(15,23,42,0.08);
        }
        #board-wrap.board-error-flash {
            animation: board-error-pulse 0.55s ease;
        }
        @keyframes board-error-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239,68,68,0); }
            35% { box-shadow: 0 0 0 3px rgba(239,68,68,0.55), 0 0 36px rgba(239,68,68,0.35); }
        }
    </style>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-sm text-slate-600 leading-tight tracking-widest uppercase dark:text-slate-500">Solve: {{ $puzzle->title }}</h2>
            @if($alreadySolved)
                <span class="inline-flex items-center gap-1.5 rounded-full border border-amber-300 bg-amber-50 px-3 py-1 text-xs font-bold text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-400"
                      style="box-shadow: 0 0 12px rgba(245,158,11,0.2);">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    Solved
                </span>
            @endif
        </div>
    </x-slot>

    <div class="py-8 px-4 max-w-5xl mx-auto">

                        <a href="{{ route('puzzles.index') }}" class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-slate-600 hover:text-red-400 mb-6 uppercase tracking-widest transition-colors duration-200">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
            All Puzzles
        </a>

        {{-- Solved Banner --}}
        <div id="solved-banner" class="{{ $alreadySolved ? '' : 'hidden' }} mb-6 flex items-center gap-4 rounded-3xl border border-amber-200/80 bg-amber-50 px-6 py-5 dark:border-amber-500/20 dark:bg-amber-500/[0.07]"
             style="{{ $alreadySolved ? 'box-shadow: 0 0 30px rgba(245,158,11,0.12);' : '' }}">
            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full border border-amber-300 bg-amber-100 dark:border-amber-500/25 dark:bg-amber-500/15"
                 style="box-shadow: 0 0 16px rgba(245,158,11,0.25);">
                <svg class="h-5 w-5 text-amber-600 dark:text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <p class="text-base font-bold text-slate-900 dark:text-white">Puzzle Solved!</p>
                <p class="text-sm text-amber-700/80 dark:text-amber-400/60">Excellent play.</p>
            </div>
        </div>

        {{-- Meta row --}}
        <div class="flex items-center gap-4 mb-5">
            <span class="text-[10px] font-semibold text-slate-600 uppercase tracking-widest">Difficulty:</span>
            <span class="text-xs font-bold uppercase tracking-wider text-amber-700 dark:text-amber-400">{{ $puzzle->difficulty }}</span>
            <span id="step-label" class="ml-auto text-xs font-medium text-slate-600 dark:text-slate-500"></span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">

            {{-- Board --}}
            <div>
                <div id="board-wrap" class="opponent-turn w-full max-w-[480px]">
                    <div id="board" class="w-full"></div>
                </div>

                {{-- Board controls --}}
                <div class="mt-4 flex gap-2">
                    <button type="button" id="btn-step-back"
                            class="rounded-xl border border-slate-200/90 bg-white px-3.5 py-1.5 text-xs font-medium text-slate-600 transition-all duration-300 hover:border-slate-300 hover:text-slate-900 dark:border-white/[0.08] dark:bg-transparent dark:text-slate-500 dark:hover:border-white/[0.16] dark:hover:text-slate-200">
                        ← Step back
                    </button>
                    <button type="button" id="btn-reset"
                            class="rounded-xl border border-slate-200/90 bg-white px-3.5 py-1.5 text-xs font-medium text-slate-600 transition-all duration-300 hover:border-slate-300 hover:text-slate-900 dark:border-white/[0.08] dark:bg-transparent dark:text-slate-500 dark:hover:border-white/[0.16] dark:hover:text-slate-200">
                        Reset
                    </button>
                    <button type="button" id="btn-solution"
                            class="px-3.5 py-1.5 bg-transparent border border-amber-500/25 text-amber-500/60 hover:border-amber-500/50 hover:text-amber-400 hover:shadow-[0_0_12px_rgba(245,158,11,0.15)] rounded-xl text-xs font-medium transition-all duration-300">
                        Show Solution
                    </button>
                </div>
            </div>

            {{-- Right panel --}}
            <div class="space-y-4">

                {{-- Feedback --}}
                <div id="feedback" class="hidden rounded-2xl px-5 py-3.5 text-sm font-medium border transition-all duration-300"></div>

                {{-- Drag instructions --}}
                <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-sm dark:border-white/[0.07] dark:bg-white/[0.03] dark:shadow-none">
                    <p class="mb-1.5 text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-600 dark:text-slate-600">How to solve</p>
                    <p class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                        Drag your pieces to the correct square. Each move must match the puzzle line. Wrong moves snap back; when you are right, the opponent reply plays automatically after a short pause.
                    </p>
                </div>

                {{-- Moves log --}}
                <div class="rounded-3xl border border-slate-200/90 bg-white p-5 shadow-sm dark:border-white/[0.07] dark:bg-white/[0.03] dark:shadow-none">
                    <p class="mb-2 text-[10px] font-semibold uppercase tracking-[0.16em] text-slate-600 dark:text-slate-600">Moves So Far</p>
                    <p id="played-moves" class="font-mono text-sm text-slate-600 dark:text-slate-500">&mdash;</p>
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
        var startFen    = @json($puzzle->initial_fen);
        var solution    = @json($puzzle->solution ?? []);
        var completeUrl = "{{ route('puzzles.complete', $puzzle) }}";
        var attemptUrl  = "{{ route('puzzles.attempt', $puzzle) }}";
        var csrf        = document.querySelector('meta[name="csrf-token"]').content;
        var solved      = @json($alreadySolved);

        var boardWrap = document.getElementById('board-wrap');
        var opponentTimer = null;
        var waitingReply = false;
        var animatingSolution = false;

        function clearOpponentTimer() {
            if (opponentTimer) {
                clearTimeout(opponentTimer);
                opponentTimer = null;
            }
        }

        function cancelOpponentWait() {
            clearOpponentTimer();
            waitingReply = false;
        }

        function setTurn(isPlayer) {
            boardWrap.classList.remove('player-turn', 'opponent-turn');
            boardWrap.classList.add(isPlayer ? 'player-turn' : 'opponent-turn');
        }

        function fullFen(f) {
            f = (f || '').trim();
            if (!f) return 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';
            return f.split(/\s+/).length >= 6 ? f : f.split(/\s+/)[0] + ' w - - 0 1';
        }

        var game = Chess();
        if (!game.load(fullFen(startFen))) game.reset();

        var fenParts = fullFen(startFen).split(/\s+/);
        var boardOrientation = fenParts[1] === 'b' ? 'black' : 'white';

        function onDragStart(source, piece) {
            if (solved || animatingSolution || waitingReply) return false;
            if (!solution.length || game.history().length >= solution.length) return false;
            if ((game.turn() === 'w' && piece.indexOf('w') !== 0) || (game.turn() === 'b' && piece.indexOf('b') !== 0)) return false;
        }

        function same(a, b) {
            return (a || '').replace(/[+#]/g, '').toLowerCase() === (b || '').replace(/[+#]/g, '').toLowerCase();
        }

        function flashBoardError() {
            boardWrap.classList.remove('board-error-flash');
            void boardWrap.offsetWidth;
            boardWrap.classList.add('board-error-flash');
            setTimeout(function () { boardWrap.classList.remove('board-error-flash'); }, 600);
        }

        var board = Chessboard('board', {
            position: game.fen().split(' ')[0],
            draggable: true,
            orientation: boardOrientation,
            dropOffBoard: 'snapback',
            pieceTheme: @json(asset('img/chesspieces/wikipedia').'/{piece}.png'),
            moveSpeed: 300,
            snapbackSpeed: 200,
            onDragStart: onDragStart,
            onDrop: onDrop,
        });

        var feedbackEl = document.getElementById('feedback');
        var stepEl     = document.getElementById('step-label');
        var movesList  = document.getElementById('played-moves');

        function show(ok, txt) {
            feedbackEl.classList.remove('hidden');
            if (ok) {
                feedbackEl.className = 'rounded-2xl px-5 py-3.5 text-sm font-medium border transition-all duration-300 bg-amber-50 border-amber-200 text-amber-900 dark:bg-amber-500/[0.07] dark:border-amber-500/20 dark:text-amber-400';
            } else {
                feedbackEl.className = 'rounded-2xl px-5 py-3.5 text-sm font-medium border transition-all duration-300 bg-red-50 border-red-200 text-red-800 dark:bg-red-500/[0.07] dark:border-red-500/20 dark:text-red-400';
            }
            feedbackEl.textContent = txt;
        }
        function hide() { feedbackEl.classList.add('hidden'); }

        function refresh() {
            var n = game.history().length;
            if (!solution.length) {
                stepEl.textContent = 'No moves in solution.';
            } else {
                stepEl.textContent = n >= solution.length ? 'Puzzle complete.' : 'Move ' + (n + 1) + ' of ' + solution.length;
            }
            movesList.textContent = n === 0 ? '—' : game.history().join(', ');
        }

        function markSolved() {
            document.getElementById('solved-banner').classList.remove('hidden');
            document.getElementById('solved-banner').style.boxShadow = '0 0 30px rgba(245,158,11,0.1)';
            setTurn(false);
            if (!solved) {
                solved = true;
                fetch(completeUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: '{}',
                });
            }
        }

        function attempt() {
            fetch(attemptUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: '{}',
            });
        }

        function scheduleOpponentReply() {
            if (game.history().length >= solution.length) {
                show(true, 'Puzzle Solved!');
                markSolved();
                return;
            }
            clearOpponentTimer();
            waitingReply = true;
            setTurn(false);
            show(true, 'Good move — opponent is thinking...');
            opponentTimer = setTimeout(function () {
                opponentTimer = null;
                var reply = game.move(solution[game.history().length], { sloppy: true });
                if (!reply) {
                    waitingReply = false;
                    setTurn(true);
                    show(false, 'Could not play the next line move.');
                    refresh();
                    board.position(game.fen().split(' ')[0], true);
                    return;
                }
                board.position(game.fen().split(' ')[0], true);
                refresh();
                waitingReply = false;
                if (game.history().length >= solution.length) {
                    show(true, 'Puzzle Solved!');
                    markSolved();
                } else {
                    show(true, 'They play ' + reply.san + '. Your turn.');
                    setTurn(true);
                }
            }, 500);
        }

        function onDrop(source, target) {
            hide();
            if (source === target) return 'snapback';
            if (solved || animatingSolution || waitingReply) return 'snapback';
            if (!solution.length || game.history().length >= solution.length) return 'snapback';

            var want = solution[game.history().length];
            var move = game.move({ from: source, to: target, promotion: 'q' });

            if (!move) {
                flashBoardError();
                show(false, 'Illegal move. Try again.');
                return 'snapback';
            }
            if (!same(move.san, want)) {
                game.undo();
                flashBoardError();
                show(false, 'Try again.');
                attempt();
                return 'snapback';
            }

            board.position(game.fen().split(' ')[0], false);
            refresh();

            if (game.history().length >= solution.length) {
                show(true, 'Puzzle Solved!');
                markSolved();
                return;
            }

            scheduleOpponentReply();
        }

        document.getElementById('btn-step-back').onclick = function () {
            hide();
            cancelOpponentWait();
            if (game.history().length === 0) return;
            if (game.history().length % 2 === 0) game.undo();
            if (game.history().length > 0) game.undo();
            board.position(game.fen().split(' ')[0], false);
            if (!solved) setTurn(true);
            refresh();
        };

        document.getElementById('btn-reset').onclick = function () {
            cancelOpponentWait();
            animatingSolution = false;
            game.load(fullFen(startFen));
            board.position(game.fen().split(' ')[0], false);
            if (!solved) setTurn(true);
            hide();
            refresh();
        };

        document.getElementById('btn-solution').onclick = function () {
            cancelOpponentWait();
            animatingSolution = true;
            game.load(fullFen(startFen));
            board.position(game.fen().split(' ')[0], false);
            setTurn(false);
            hide();
            show(true, 'Playing solution...');
            refresh();
            var i = 0;
            function playNext() {
                if (i >= solution.length) {
                    animatingSolution = false;
                    show(true, 'Solution complete!');
                    return;
                }
                var move = game.move(solution[i], { sloppy: true });
                if (!move) {
                    animatingSolution = false;
                    show(false, 'Bad solution move: ' + solution[i]);
                    return;
                }
                board.position(game.fen().split(' ')[0], true);
                refresh();
                i++;
                setTimeout(playNext, 800);
            }
            setTimeout(playNext, 400);
        };

        refresh();
        if (!solution.length) {
            show(false, 'No solution moves are stored for this puzzle.');
            setTurn(false);
        } else if (!solved) {
            setTurn(true);
        } else {
            setTurn(false);
        }
    })();
    </script>
    @endpush
</x-app-layout>
