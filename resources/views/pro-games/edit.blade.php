@push('styles')
    <link rel="stylesheet" href="{{ asset('css/chessboard.css') }}">
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-white leading-tight">Edit: {{ $proGame->title ?? 'Untitled Game' }}</h2>
            <a href="{{ route('pro-games.show', $proGame) }}" class="text-sm text-emerald-400 hover:text-emerald-300 transition-colors">&larr; Back to game</a>
        </div>
    </x-slot>

    <div class="py-8 px-4 max-w-7xl mx-auto">
        @if (session('success'))
            <div class="mb-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <form id="pro-game-form" method="POST" action="{{ route('pro-games.update', $proGame) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="moves_data" id="moves_data">

            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-300 mb-1">Game Title</label>
                <input type="text" id="title" name="title" value="{{ $proGame->title }}" required
                    class="w-full max-w-md bg-white/5 border border-white/10 text-white rounded-xl px-4 py-3 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 placeholder-gray-500 transition-all duration-200">
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                <div>
                    <p class="text-sm font-medium text-gray-300 mb-2">Board</p>
                    <div id="board" class="w-full max-w-[480px] rounded-xl overflow-hidden shadow-2xl shadow-black/50 ring-1 ring-white/10"></div>
                    <div class="mt-3 flex gap-2">
                        <button type="button" id="btn-first" class="px-3 py-1.5 border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 text-sm font-mono text-gray-300 transition-all duration-200">&lt;&lt;</button>
                        <button type="button" id="btn-prev" class="px-3 py-1.5 border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 text-sm font-mono text-gray-300 transition-all duration-200">&lt;</button>
                        <button type="button" id="btn-next" class="px-3 py-1.5 border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 text-sm font-mono text-gray-300 transition-all duration-200">&gt;</button>
                        <button type="button" id="btn-last" class="px-3 py-1.5 border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 text-sm font-mono text-gray-300 transition-all duration-200">&gt;&gt;</button>
                    </div>
                    <p id="board-label" class="mt-2 text-sm text-gray-500">Start position</p>
                </div>

                <div>
                    <div id="cards-container" class="space-y-3 max-h-[600px] overflow-y-auto pr-1"></div>

                    <div class="mt-6">
                        <button type="submit" class="bg-gradient-to-r from-emerald-500 to-emerald-700 text-white px-5 py-2.5 rounded-xl hover:from-emerald-400 hover:to-emerald-600 text-sm font-medium transition-all duration-200 shadow-lg shadow-emerald-500/20">Save Changes</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script type="application/json" id="existing-moves-data">@json($proGame->moves_data)</script>
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/chess.js') }}"></script>
    <script src="{{ asset('js/chessboard.js') }}"></script>
    <script>
    (function () {
        var existingData = JSON.parse(document.getElementById('existing-moves-data').textContent);

        var chess = new Chess();
        var positions = [chess.fen()];
        var sanMoves = [];
        var generatedMoves = [];
        var currentPly = 0;

        for (var i = 0; i < existingData.length; i++) {
            var n = existingData[i].notation || existingData[i].move || '?';
            existingData[i].notation = n;
            var m = chess.move(n, { sloppy: true });
            if (m) {
                sanMoves.push(m.san);
                positions.push(chess.fen());
            }
        }

        var board = Chessboard('board', {
            position: positions[positions.length - 1].split(' ')[0],
            draggable: false,
            pieceTheme: "{{ asset('img/chesspieces/wikipedia') }}/{piece}.png",
        });

        var boardLabel = document.getElementById('board-label');
        var cardsContainer = document.getElementById('cards-container');
        var form = document.getElementById('pro-game-form');
        var movesDataInput = document.getElementById('moves_data');

        function goToPly(ply) {
            if (ply < 0) ply = 0;
            if (ply > positions.length - 1) ply = positions.length - 1;
            currentPly = ply;
            board.position(positions[ply].split(' ')[0], true);
            if (ply === 0) {
                boardLabel.textContent = 'Start position';
            } else {
                var moveNum = Math.ceil(ply / 2);
                var side = ply % 2 === 1 ? '.' : '...';
                boardLabel.textContent = moveNum + side + ' ' + sanMoves[ply - 1];
            }
            var cards = document.querySelectorAll('.move-card');
            cards.forEach(function (c, idx) {
                if (idx === currentPly - 1) c.classList.add('ring-2', 'ring-emerald-400');
                else c.classList.remove('ring-2', 'ring-emerald-400');
            });
        }

        document.getElementById('btn-first').onclick = function () { goToPly(0); };
        document.getElementById('btn-prev').onclick = function () { goToPly(currentPly - 1); };
        document.getElementById('btn-next').onclick = function () { goToPly(currentPly + 1); };
        document.getElementById('btn-last').onclick = function () { goToPly(positions.length - 1); };

        existingData.forEach(function (entry, index) {
            var notation = entry.notation;
            var score = entry.score || 0;
            var isBrilliant = entry.is_brilliant || false;
            var isBlunder = entry.is_blunder || entry.isImportant || false;
            var manualDesc = entry.manual_desc || entry.description || '';

            var obj = {
                notation: notation, score: score,
                is_brilliant: isBrilliant, is_blunder: isBlunder,
                manual_desc: manualDesc
            };
            generatedMoves.push(obj);

            var card = document.createElement('div');
            card.className = 'move-card p-3 rounded-xl border border-white/10 cursor-pointer transition-all bg-white/5 hover:bg-white/10';
            if (isBrilliant) { card.classList.add('border-blue-400/30', 'bg-blue-500/10'); card.classList.remove('border-white/10', 'bg-white/5'); }
            else if (isBlunder) { card.classList.add('border-red-400/30'); card.classList.remove('border-white/10'); }
            card.dataset.ply = index + 1;
            card.onclick = function () { goToPly(parseInt(this.dataset.ply, 10)); };

            var moveNum = Math.ceil((index + 1) / 2);
            var dot = (index % 2 === 0) ? '.' : '...';

            var row = document.createElement('div');
            row.className = 'flex items-center gap-2';

            var dot_el = document.createElement('span');
            if (isBrilliant) dot_el.className = 'inline-block w-3 h-3 flex-shrink-0 bg-blue-400 rotate-45';
            else if (isBlunder) dot_el.className = 'inline-block w-3 h-3 rounded-full flex-shrink-0 bg-red-400';
            else dot_el.className = 'inline-block w-3 h-3 rounded-full flex-shrink-0 bg-emerald-400';
            dot_el.id = 'dot-' + index;
            row.appendChild(dot_el);

            var label = document.createElement('span');
            label.className = 'font-bold text-white text-sm flex-1';
            label.textContent = moveNum + dot + ' ' + notation;
            row.appendChild(label);

            var sign = score > 0 ? '+' : '';
            var badge = document.createElement('span');
            badge.className = 'text-xs font-mono px-2 py-0.5 rounded-lg bg-white/10 text-gray-400';
            badge.textContent = sign + score.toFixed(1);
            row.appendChild(badge);
            card.appendChild(row);

            var brilliantRow = document.createElement('div');
            brilliantRow.className = 'mt-2 flex items-center gap-2';
            var cb = document.createElement('input');
            cb.type = 'checkbox';
            cb.id = 'brilliant-' + index;
            cb.className = 'rounded bg-white/5 border-white/20 text-blue-500 focus:ring-blue-500';
            cb.checked = isBrilliant;
            cb.dataset.index = index;
            cb.onclick = function (e) {
                e.stopPropagation();
                var idx = parseInt(this.dataset.index, 10);
                generatedMoves[idx].is_brilliant = this.checked;
                var d = document.getElementById('dot-' + idx);
                var c = this.closest('.move-card');
                if (this.checked) {
                    d.className = 'inline-block w-3 h-3 flex-shrink-0 bg-blue-400 rotate-45';
                    c.classList.add('border-blue-400/30', 'bg-blue-500/10');
                    c.classList.remove('border-white/10', 'bg-white/5');
                } else {
                    c.classList.remove('border-blue-400/30', 'bg-blue-500/10');
                    c.classList.add('border-white/10', 'bg-white/5');
                    if (generatedMoves[idx].is_blunder) d.className = 'inline-block w-3 h-3 rounded-full flex-shrink-0 bg-red-400';
                    else d.className = 'inline-block w-3 h-3 rounded-full flex-shrink-0 bg-emerald-400';
                }
            };
            var cbLabel = document.createElement('label');
            cbLabel.htmlFor = 'brilliant-' + index;
            cbLabel.className = 'text-xs text-blue-400 font-semibold cursor-pointer';
            cbLabel.textContent = 'Brilliant';
            cbLabel.onclick = function (e) { e.stopPropagation(); };
            brilliantRow.appendChild(cb);
            brilliantRow.appendChild(cbLabel);
            card.appendChild(brilliantRow);

            var manualLbl = document.createElement('label');
            manualLbl.className = 'block text-xs font-medium text-gray-500 mt-2';
            manualLbl.textContent = 'Your description:';
            card.appendChild(manualLbl);

            var manualTa = document.createElement('textarea');
            manualTa.className = 'w-full bg-white/5 border border-white/10 text-white rounded-lg px-2 py-1 mt-1 text-sm placeholder-gray-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500';
            manualTa.rows = 2;
            manualTa.placeholder = 'Write your own explanation...';
            manualTa.value = manualDesc;
            manualTa.id = 'manual-' + index;
            manualTa.dataset.index = index;
            manualTa.oninput = function () { generatedMoves[parseInt(this.dataset.index, 10)].manual_desc = this.value; };
            manualTa.onclick = function (e) { e.stopPropagation(); };
            card.appendChild(manualTa);

            cardsContainer.appendChild(card);
        });

        goToPly(positions.length - 1);

        form.onsubmit = function () {
            movesDataInput.value = JSON.stringify(generatedMoves);
        };
    })();
    </script>
    @endpush
</x-app-layout>
