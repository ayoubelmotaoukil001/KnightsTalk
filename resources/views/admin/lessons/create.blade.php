@push('styles')
    <link rel="stylesheet" href="{{ asset('css/chessboard.css') }}">
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-white">New Lesson &mdash; {{ $course->title }}</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl px-4 py-8">

        <a href="{{ route('admin.courses.show', $course) }}" class="mb-6 inline-block text-sm font-medium text-red-600 transition-colors hover:text-red-500 dark:text-red-400 dark:hover:text-red-300">&larr; Back to Course</a>

        <form id="lesson-form" method="POST" action="{{ route('admin.lessons.store', $course) }}">
            @csrf
            <input type="hidden" name="moves_sequence" id="h_moves_seq">
            <input type="hidden" name="moves_data" id="h_moves_data">

            <div class="mb-6">
                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Lesson Title</label>
                <input type="text" name="title" required class="w-full max-w-md rounded-xl border border-slate-200/90 bg-white px-4 py-3 text-slate-900 transition-all duration-200 placeholder:text-slate-400 focus:border-red-500 focus:ring-1 focus:ring-red-500 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-red-400 dark:focus:ring-red-400">
            </div>

            <div class="grid grid-cols-1 items-start gap-8 lg:grid-cols-2">
                <div>
                    <p class="mb-2 text-sm font-medium text-slate-700 dark:text-slate-300">Board</p>
                    <div id="board" style="width:480px;max-width:100%" class="overflow-hidden rounded-xl shadow-lg ring-1 ring-slate-200 dark:shadow-2xl dark:shadow-black/50 dark:ring-white/10"></div>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button type="button" id="btn-first" class="rounded-lg border border-slate-200/90 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 transition-all duration-200 hover:bg-slate-50 dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">&lt;&lt;</button>
                        <button type="button" id="btn-prev"  class="rounded-lg border border-slate-200/90 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 transition-all duration-200 hover:bg-slate-50 dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">&lt;</button>
                        <button type="button" id="btn-next"  class="rounded-lg border border-slate-200/90 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 transition-all duration-200 hover:bg-slate-50 dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">&gt;</button>
                        <button type="button" id="btn-last"  class="rounded-lg border border-slate-200/90 bg-white px-3 py-1.5 text-sm font-medium text-slate-700 transition-all duration-200 hover:bg-slate-50 dark:border-white/10 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">&gt;&gt;</button>
                    </div>
                    <p id="board-label" class="mt-2 text-sm text-slate-600 dark:text-slate-500">Start position</p>
                </div>

                <div>
                    <div class="mb-4">
                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Move Sequence</label>
                        <textarea id="move_input" rows="3" placeholder="e.g. e4 e5 Nf3 Nc6 Bb5 a6"
                            class="w-full rounded-xl border border-slate-200/90 bg-white px-4 py-3 font-mono text-sm text-slate-900 transition-all duration-200 placeholder:text-slate-400 focus:border-red-500 focus:ring-1 focus:ring-red-500 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-red-400 dark:focus:ring-red-400"></textarea>
                    </div>
                    <button type="button" id="btn-generate" class="mb-4 rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-all duration-200 hover:bg-blue-500">Generate Cards</button>
                    <div id="cards" class="max-h-[600px] space-y-3 overflow-y-auto"></div>
                    <div id="save-section" class="mt-6 hidden">
                        <button type="submit" class="rounded-xl border border-red-500/50 bg-gradient-to-r from-red-500 to-red-600 px-5 py-2.5 text-sm font-medium text-white shadow-md transition-all duration-200 hover:from-red-400 hover:to-red-500 dark:shadow-lg dark:shadow-red-500/20">Save Lesson</button>
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
        var chess = new Chess();
        var positions = [chess.fen()];
        var sanMoves = [];
        var currentPly = 0;

        var board = Chessboard('board', { position: 'start', draggable: false, pieceTheme: "{{ asset('img/chesspieces/wikipedia') }}/{piece}.png" });

        function goToPly(ply) {
            if (ply < 0) ply = 0;
            if (ply > positions.length - 1) ply = positions.length - 1;
            currentPly = ply;
            board.position(positions[ply].split(' ')[0], true);
            document.getElementById('board-label').textContent = ply === 0 ? 'Start position' : Math.ceil(ply/2) + (ply%2===1?'.':'...') + ' ' + sanMoves[ply-1];
            document.querySelectorAll('.move-card').forEach(function(c,i){ c.style.outline = i===currentPly-1 ? '2px solid #ef4444' : 'none'; });
        }

        document.getElementById('btn-first').onclick = function(){ goToPly(0); };
        document.getElementById('btn-prev').onclick  = function(){ goToPly(currentPly-1); };
        document.getElementById('btn-next').onclick  = function(){ goToPly(currentPly+1); };
        document.getElementById('btn-last').onclick  = function(){ goToPly(positions.length-1); };

        document.getElementById('btn-generate').onclick = function () {
            var raw = document.getElementById('move_input').value.trim();
            if (!raw) { alert('Enter moves first.'); return; }

            chess.reset(); positions = [chess.fen()]; sanMoves = [];
            var moves = raw.split(/\s+/);
            for (var i = 0; i < moves.length; i++) {
                var m = chess.move(moves[i], { sloppy: true });
                if (!m) break;
                sanMoves.push(m.san);
                positions.push(chess.fen());
            }
            if (sanMoves.length === 0) { alert('No valid moves found.'); return; }

            var container = document.getElementById('cards');
            container.innerHTML = '';
            sanMoves.forEach(function (san, idx) {
                var num = Math.ceil((idx+1)/2) + (idx%2===0?'.':'...');
                var card = document.createElement('div');
                card.className = 'move-card cursor-pointer rounded-xl border border-slate-200/90 bg-white p-3 transition-all duration-200 hover:bg-slate-50 dark:border-white/10 dark:bg-white/5 dark:hover:bg-white/10';
                card.onclick = function(){ goToPly(idx+1); };
                card.innerHTML = '<p class="mb-2 text-sm font-bold text-slate-900 dark:text-white">' + num + ' ' + san + '</p>' +
                    '<textarea id="desc-'+idx+'" rows="2" placeholder="Describe this move..." class="w-full rounded-lg border border-slate-200/90 bg-white px-2 py-1 text-sm text-slate-900 placeholder:text-slate-400 focus:border-red-500 focus:ring-1 focus:ring-red-500 dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder:text-slate-500 dark:focus:border-red-400 dark:focus:ring-red-400" onclick="event.stopPropagation()"></textarea>';
                container.appendChild(card);
            });

            document.getElementById('h_moves_seq').value = sanMoves.join(' ');
            document.getElementById('save-section').classList.remove('hidden');
            goToPly(positions.length - 1);
        };

        document.getElementById('lesson-form').onsubmit = function () {
            var seq = document.getElementById('h_moves_seq').value;
            if (!seq) { alert('Generate cards first.'); return false; }
            var data = seq.split(' ').map(function(san, i) {
                var ta = document.getElementById('desc-' + i);
                return { notation: san, description: ta ? ta.value.trim() : '' };
            });
            document.getElementById('h_moves_data').value = JSON.stringify(data);
        };
    })();
    </script>
    @endpush
</x-app-layout>
