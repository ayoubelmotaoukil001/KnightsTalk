@push('styles')
    <link rel="stylesheet" href="{{ asset('css/chessboard.css') }}">
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Solve: {{ $puzzle->title }}</h2>
            @if($alreadySolved)
                <span class="px-3 py-1 bg-green-500 text-white text-sm font-bold rounded-full">Solved</span>
            @endif
        </div>
    </x-slot>

    <div class="py-8 px-4 max-w-6xl mx-auto">

        <a href="{{ route('puzzles.index') }}" class="text-blue-600 underline text-sm mb-4 inline-block">&larr; Back to puzzles</a>

        <p class="text-sm text-gray-600 mb-2">Difficulty: <strong>{{ $puzzle->difficulty }}</strong></p>
        <p id="step-label" class="text-sm font-medium text-gray-800 mb-4"></p>
        <div id="feedback" class="mb-4 text-sm font-medium hidden"></div>

        <div id="solved-banner" class="{{ $alreadySolved ? '' : 'hidden' }} mb-5 p-4 bg-green-50 border border-green-300 rounded-xl flex items-center gap-3">
            <div>
                <p class="text-green-700 font-bold text-lg">Puzzle Solved!</p>
                <p class="text-green-600 text-sm">Great job.</p>
            </div>
            <span class="ml-auto px-4 py-2 bg-green-500 text-white font-bold rounded-full text-sm">Solved</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
            <div>
                <div id="board" class="w-full max-w-[480px]"></div>
                <div class="mt-3 flex gap-2 flex-wrap">
                    <button type="button" id="btn-step-back" class="px-3 py-1 border rounded bg-white hover:bg-gray-50 text-sm">Step back</button>
                    <button type="button" id="btn-reset" class="px-3 py-1 border rounded bg-white hover:bg-gray-50 text-sm">Start over</button>
                    <button type="button" id="btn-solution" class="px-3 py-1 border border-amber-400 rounded bg-amber-50 hover:bg-amber-100 text-amber-700 text-sm font-medium">Show Solution</button>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Type your move</label>
                    <p class="text-xs text-gray-500 mt-1">Type <strong>your</strong> moves only. Wrong move: try again.</p>
                    <div class="mt-2 flex gap-2 items-center">
                        <input type="text" id="move_input" autocomplete="off" class="border rounded px-2 py-1 font-mono text-sm w-48" placeholder="e.g. e4" {{ $alreadySolved ? 'disabled' : '' }}>
                        <button type="button" id="btn-submit" class="px-4 py-2 bg-gray-800 text-white rounded text-sm hover:bg-gray-700" {{ $alreadySolved ? 'disabled' : '' }}>Submit</button>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-700">Moves so far</p>
                    <p id="played-moves" class="mt-1 text-sm font-mono text-gray-600">&mdash;</p>
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

        function fullFen(f) {
            f = (f||'').trim();
            if (!f) return 'rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1';
            return f.split(/\s+/).length >= 6 ? f : f.split(/\s+/)[0] + ' w - - 0 1';
        }

        var game = Chess();
        if (!game.load(fullFen(startFen))) game.reset();

        var board = Chessboard('board', {
            position: game.fen().split(' ')[0], draggable: false,
            pieceTheme: @json(asset('img/chesspieces/wikipedia').'/{piece}.png'),
            moveSpeed: 300,
        });

        var msg   = document.getElementById('feedback');
        var step  = document.getElementById('step-label');
        var input = document.getElementById('move_input');
        var list  = document.getElementById('played-moves');
        var btnGo = document.getElementById('btn-submit');

        function show(ok, txt) { msg.classList.remove('hidden'); msg.style.color = ok?'green':'red'; msg.textContent = txt; }
        function hide()        { msg.classList.add('hidden'); }

        function refresh() {
            var n = game.history().length;
            step.textContent = n >= solution.length ? 'Puzzle complete.' : 'Move '+(n+1)+' of '+solution.length;
            list.textContent = n === 0 ? '—' : game.history().join(', ');
        }

        function same(a,b) { return (a||'').replace(/[+#]/g,'').toLowerCase() === (b||'').replace(/[+#]/g,'').toLowerCase(); }

        function normalize(s) {
            s = (s||'').trim(); if (!s) return s;
            var c = s.replace(/0/g,'O').replace(/\s/g,'');
            if (/^o-o-o$/i.test(c)) return 'O-O-O';
            if (/^o-o$/i.test(c)) return 'O-O';
            var ch = s[0];
            if ('nrqk'.includes(ch)) return ch.toUpperCase()+s.slice(1);
            if (ch==='b' && !/^bx[a-h][1-8]/.test(s) && !/^b[1-8]/.test(s)) return 'B'+s.slice(1);
            return s;
        }

        function markSolved() {
            document.getElementById('solved-banner').classList.remove('hidden');
            input.disabled = true; btnGo.disabled = true;
            if (!solved) { solved = true; fetch(completeUrl, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf}, body:'{}' }); }
        }

        function attempt() { fetch(attemptUrl, { method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrf}, body:'{}' }); }

        function onSubmit() {
            hide();
            if (game.history().length >= solution.length) { show(true,'Already finished.'); return; }
            var typed = normalize(input.value);
            if (!typed) { show(false,'Type a move.'); return; }

            var want = solution[game.history().length];
            var played = game.move(typed, { sloppy: true });
            if (!played) { show(false,'Illegal move.'); return; }
            if (!same(played.san, want)) { game.undo(); show(false,'Wrong move. Try again.'); attempt(); return; }

            board.position(game.fen().split(' ')[0], true);
            input.value = ''; input.disabled = true; btnGo.disabled = true;
            refresh();

            if (game.history().length >= solution.length) { show(true,'You solved it!'); markSolved(); return; }

            show(true,'Good move...');
            setTimeout(function () {
                var reply = game.move(solution[game.history().length], { sloppy: true });
                if (!reply) { input.disabled=false; btnGo.disabled=false; refresh(); return; }
                board.position(game.fen().split(' ')[0], true);
                refresh();
                if (game.history().length >= solution.length) { show(true,'You solved it!'); markSolved(); }
                else { show(true,'They play '+reply.san+'. Your turn.'); input.disabled=false; btnGo.disabled=false; input.focus(); }
            }, 750);
        }

        btnGo.onclick = onSubmit;
        input.onkeydown = function(e){ if(e.key==='Enter') onSubmit(); };

        document.getElementById('btn-step-back').onclick = function () {
            hide();
            if (game.history().length===0) return;
            if (game.history().length%2===0) game.undo();
            if (game.history().length>0) game.undo();
            board.position(game.fen().split(' ')[0], false);
            if (!solved) { input.disabled=false; btnGo.disabled=false; }
            refresh(); input.focus();
        };

        document.getElementById('btn-reset').onclick = function () {
            game.load(fullFen(startFen));
            board.position(game.fen().split(' ')[0], false);
            input.value = '';
            if (!solved) { input.disabled=false; btnGo.disabled=false; }
            hide(); refresh(); input.focus();
        };

        document.getElementById('btn-solution').onclick = function () {
            // Reset to start
            game.load(fullFen(startFen));
            board.position(game.fen().split(' ')[0], false);
            input.disabled = true;
            btnGo.disabled = true;
            input.value = '';
            show(true, 'Playing solution...');
            refresh();

            // Play each solution move one by one
            var i = 0;
            function playNext() {
                if (i >= solution.length) {
                    show(true, 'Solution complete!');
                    return;
                }
                var move = game.move(solution[i], { sloppy: true });
                if (!move) { show(false, 'Bad solution move: ' + solution[i]); return; }
                board.position(game.fen().split(' ')[0], true);
                refresh();
                i++;
                setTimeout(playNext, 800);
            }
            setTimeout(playNext, 400);
        };

        refresh();
        if (!solved) input.focus();
    })();
    </script>
    @endpush
</x-app-layout>
