@push('styles')
    <link rel="stylesheet" href="{{ asset('css/chessboard.css') }}">
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">New Lesson &mdash; {{ $course->title }}</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-7xl mx-auto">

        <a href="{{ route('admin.courses.show', $course) }}" class="text-emerald-400 hover:text-emerald-300 text-sm mb-6 inline-block transition-colors">&larr; Back to Course</a>

        <form id="lesson-form" method="POST" action="{{ route('admin.lessons.store', $course) }}">
            @csrf
            <input type="hidden" name="moves_sequence" id="h_moves_seq">
            <input type="hidden" name="moves_data" id="h_moves_data">

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-300 mb-1">Lesson Title</label>
                <input type="text" name="title" required class="w-full max-w-md bg-white/5 border border-white/10 text-white rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 placeholder-gray-500 transition-all duration-200">
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                <div>
                    <p class="text-sm font-medium text-gray-300 mb-2">Board</p>
                    <div id="board" style="width:480px" class="rounded-xl overflow-hidden shadow-2xl shadow-black/50 ring-1 ring-white/10"></div>
                    <div class="mt-3 flex gap-2">
                        <button type="button" id="btn-first" class="px-3 py-1.5 border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 text-sm text-gray-300 transition-all duration-200">&lt;&lt;</button>
                        <button type="button" id="btn-prev"  class="px-3 py-1.5 border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 text-sm text-gray-300 transition-all duration-200">&lt;</button>
                        <button type="button" id="btn-next"  class="px-3 py-1.5 border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 text-sm text-gray-300 transition-all duration-200">&gt;</button>
                        <button type="button" id="btn-last"  class="px-3 py-1.5 border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 text-sm text-gray-300 transition-all duration-200">&gt;&gt;</button>
                    </div>
                    <p id="board-label" class="mt-2 text-sm text-gray-500">Start position</p>
                </div>

                <div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-300 mb-1">Move Sequence</label>
                        <textarea id="move_input" rows="3" placeholder="e.g. e4 e5 Nf3 Nc6 Bb5 a6"
                            class="w-full bg-white/5 border border-white/10 text-white rounded-xl px-4 py-3 font-mono text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 placeholder-gray-500 transition-all duration-200"></textarea>
                    </div>
                    <button type="button" id="btn-generate" class="mb-4 bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-500 text-sm font-medium transition-all duration-200">Generate Cards</button>
                    <div id="cards" class="space-y-3 max-h-[600px] overflow-y-auto"></div>
                    <div id="save-section" class="hidden mt-6">
                        <button type="submit" class="bg-gradient-to-r from-emerald-500 to-emerald-700 text-white px-5 py-2.5 rounded-xl hover:from-emerald-400 hover:to-emerald-600 text-sm font-medium transition-all duration-200 shadow-lg shadow-emerald-500/20">Save Lesson</button>
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
            document.querySelectorAll('.move-card').forEach(function(c,i){ c.style.outline = i===currentPly-1 ? '2px solid #10b981' : 'none'; });
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
                card.className = 'move-card p-3 rounded-xl border border-white/10 bg-white/5 cursor-pointer transition-all duration-200 hover:bg-white/10';
                card.onclick = function(){ goToPly(idx+1); };
                card.innerHTML = '<p class="font-bold text-sm mb-2 text-white">' + num + ' ' + san + '</p>' +
                    '<textarea id="desc-'+idx+'" rows="2" placeholder="Describe this move..." class="w-full bg-white/5 border border-white/10 text-white rounded-lg px-2 py-1 text-sm placeholder-gray-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" onclick="event.stopPropagation()"></textarea>';
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
