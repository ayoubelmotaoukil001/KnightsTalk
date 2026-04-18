@push('styles')
    <link rel="stylesheet" href="{{ asset('css/chessboard.css') }}">
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">Pro Game Creator</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-7xl mx-auto">
        @if (session('success'))
            <div class="mb-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        <form id="pro-game-form" method="POST" action="{{ route('pro-games.store') }}">
            @csrf
            <input type="hidden" name="moves_data" id="moves_data">

            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-300 mb-1">Game Title</label>
                <input type="text" id="title" name="title" required
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
                    <div class="mb-4">
                        <label for="move_sequence" class="block text-sm font-medium text-gray-300 mb-1">Move Sequence</label>
                        <textarea id="move_sequence" rows="3" placeholder="e.g. e4 e5 Nf3 Nc6 Bb5 a6"
                            class="w-full bg-white/5 border border-white/10 text-white rounded-xl px-4 py-3 font-mono text-sm focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 placeholder-gray-500 transition-all duration-200"></textarea>
                    </div>

                    <div class="mb-4 flex flex-wrap gap-2">
                        <button type="button" id="btn-generate" class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-500 text-sm font-medium transition-all duration-200">Generate Cards</button>
                    </div>

                    <div class="mb-4 hidden" id="progress-section">
                        <p class="text-sm text-gray-400 mb-1" id="progress-label">Analyzing...</p>
                        <div class="w-full bg-white/5 rounded-full h-2.5">
                            <div id="progress-bar" class="bg-emerald-500 h-2.5 rounded-full transition-all duration-300 shadow-[0_0_8px_rgba(16,185,129,0.4)]" style="width: 0%"></div>
                        </div>
                    </div>

                    <div id="cards-container" class="space-y-3 max-h-[600px] overflow-y-auto pr-1"></div>

                    <div id="save-section" class="hidden mt-6">
                        <button type="submit" class="bg-gradient-to-r from-emerald-500 to-emerald-700 text-white px-5 py-2.5 rounded-xl hover:from-emerald-400 hover:to-emerald-600 text-sm font-medium transition-all duration-200 shadow-lg shadow-emerald-500/20">Save Pro Game</button>
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
        var generatedMoves = [];
        var currentPly = 0;

        function normalizeMove(raw) {
            var s = (raw || '').trim();
            if (s === '') return s;
            var castle = s.replace(/0/g, 'O').replace(/\s/g, '');
            if (/^o-o-o$/i.test(castle)) return 'O-O-O';
            if (/^o-o$/i.test(castle)) return 'O-O';
            if (/=[qrbnQRBN]$/.test(s)) s = s.slice(0, -1) + s.charAt(s.length - 1).toUpperCase();
            var c = s.charAt(0);
            if ('nrqkNRQK'.indexOf(c) !== -1) return c.toUpperCase() + s.slice(1);
            if (c === 'b' || c === 'B') {
                if (/^[bB]x[a-h][1-8]/.test(s) || /^[bB][1-8]/.test(s)) return s;
                return 'B' + s.slice(1);
            }
            return s;
        }

        var board = Chessboard('board', {
            position: 'start',
            draggable: false,
            pieceTheme: "{{ asset('img/chesspieces/wikipedia') }}/{piece}.png",
        });

        var boardLabel = document.getElementById('board-label');

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
            highlightActiveCard();
        }

        function highlightActiveCard() {
            var cards = document.querySelectorAll('.move-card');
            cards.forEach(function (c, i) {
                if (i === currentPly - 1) {
                    c.classList.add('ring-2', 'ring-emerald-400');
                } else {
                    c.classList.remove('ring-2', 'ring-emerald-400');
                }
            });
        }

        document.getElementById('btn-first').onclick = function () { goToPly(0); };
        document.getElementById('btn-prev').onclick = function () { goToPly(currentPly - 1); };
        document.getElementById('btn-next').onclick = function () { goToPly(currentPly + 1); };
        document.getElementById('btn-last').onclick = function () { goToPly(positions.length - 1); };

        var btnGenerate = document.getElementById('btn-generate');
        var cardsContainer = document.getElementById('cards-container');
        var saveSection = document.getElementById('save-section');
        var progressSection = document.getElementById('progress-section');
        var progressBar = document.getElementById('progress-bar');
        var progressLabel = document.getElementById('progress-label');
        var form = document.getElementById('pro-game-form');
        var movesDataInput = document.getElementById('moves_data');

        btnGenerate.onclick = function () {
            var sequence = document.getElementById('move_sequence').value.trim();
            if (sequence === '') return;
            var rawMoves = sequence.split(/\s+/);

            chess.reset();
            positions = [chess.fen()];
            sanMoves = [];
            generatedMoves = [];
            cardsContainer.innerHTML = '';
            saveSection.classList.add('hidden');
            progressSection.classList.remove('hidden');
            progressBar.style.width = '0%';
            progressLabel.textContent = 'Loading moves...';

            var sf = new Worker('/js/stockfish.js');
            sf.postMessage('uci');
            var evalIndex = 0;
            var evals = [];
            var lastScore = 0;
            var analysisTimeout = null;

            var validMoves = [];
            for (var i = 0; i < rawMoves.length; i++) {
                var m = chess.move(normalizeMove(rawMoves[i]), { sloppy: true });
                if (!m) break;
                validMoves.push(m.san);
                sanMoves.push(m.san);
                positions.push(chess.fen());
            }
            if (validMoves.length === 0) { progressSection.classList.add('hidden'); return; }
            chess.reset();

            function advanceEval(score) {
                if (analysisTimeout !== null) { clearTimeout(analysisTimeout); analysisTimeout = null; }
                evals.push(score);
                evalIndex++;
                var pct = evalIndex >= validMoves.length ? 100 : Math.round((evalIndex / validMoves.length) * 100);
                progressBar.style.width = pct + '%';
                progressLabel.textContent = 'Stockfish: ' + evalIndex + ' / ' + validMoves.length;
                analyzeNext();
            }

            function analyzeNext() {
                if (evalIndex >= validMoves.length) {
                    progressBar.style.width = '100%';
                    progressLabel.textContent = 'Analysis complete!';
                    sf.terminate();
                    buildCards(validMoves, evals);
                    return;
                }

                var sanMove = validMoves[evalIndex];
                var result = chess.move(sanMove, { sloppy: true });
                if (!result) result = chess.move(sanMove.replace(/#/g, ''), { sloppy: true });
                if (!result) { advanceEval(lastScore); return; }

                if (chess.in_checkmate()) {
                    lastScore = chess.turn() === 'b' ? 100 : -100;
                    advanceEval(lastScore);
                    return;
                }
                if (chess.game_over()) {
                    advanceEval(0);
                    return;
                }

                sf.postMessage('position fen ' + chess.fen());
                sf.postMessage('go depth 12');

                analysisTimeout = setTimeout(function () {
                    advanceEval(lastScore);
                }, 2000);
            }

            sf.onmessage = function (e) {
                var matchCp = e.data.match(/score cp (-?\d+)/);
                var matchMate = e.data.match(/score mate (-?\d+)/);
                if (matchCp) {
                    lastScore = parseInt(matchCp[1], 10) / 100.0;
                    if (chess.turn() === 'b') lastScore = -lastScore;
                } else if (matchMate) {
                    var mate = parseInt(matchMate[1], 10);
                    if (mate === 0) {
                        lastScore = chess.turn() === 'b' ? 100 : -100;
                    } else {
                        lastScore = mate > 0 ? 100 : -100;
                        if (chess.turn() === 'b') lastScore = -lastScore;
                    }
                }
                if (e.data.indexOf('bestmove') === 0) {
                    advanceEval(lastScore);
                }
            };
            analyzeNext();
        };

        function getQuality(score, prevScore) {
            var diff = score - prevScore;
            if (diff < -1.5) return 'blunder';
            if (diff > 1.5) return 'blunder';
            return 'best';
        }

        function buildCards(moves, evals) {
            progressSection.classList.add('hidden');
            generatedMoves = [];

            moves.forEach(function (san, index) {
                var score = evals[index] || 0;
                var prevScore = index === 0 ? 0.3 : (evals[index - 1] || 0);
                var quality = getQuality(score, prevScore);

                var entry = {
                    notation: san, score: score,
                    is_brilliant: false, is_blunder: quality === 'blunder',
                    manual_desc: ''
                };
                generatedMoves.push(entry);

                var card = document.createElement('div');
                card.className = 'move-card p-3 rounded-xl border border-white/10 cursor-pointer transition-all bg-white/5 hover:bg-white/10';
                card.dataset.ply = index + 1;
                card.dataset.index = index;
                card.onclick = function () { goToPly(parseInt(this.dataset.ply, 10)); };

                var moveNum = Math.ceil((index + 1) / 2);
                var dot = (index % 2 === 0) ? '.' : '...';

                var row = document.createElement('div');
                row.className = 'flex items-center gap-2';

                var dot_el = document.createElement('span');
                dot_el.className = 'inline-block w-3 h-3 rounded-full flex-shrink-0 ';
                if (quality === 'blunder') dot_el.className += 'bg-red-400';
                else dot_el.className += 'bg-emerald-400';
                dot_el.id = 'dot-' + index;
                row.appendChild(dot_el);

                var label = document.createElement('span');
                label.className = 'font-bold text-white text-sm flex-1';
                label.textContent = moveNum + dot + ' ' + san;
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

                var manualLabel = document.createElement('label');
                manualLabel.className = 'block text-xs font-medium text-gray-500 mt-2';
                manualLabel.textContent = 'Your description:';
                card.appendChild(manualLabel);

                var manualTa = document.createElement('textarea');
                manualTa.className = 'w-full bg-white/5 border border-white/10 text-white rounded-lg px-2 py-1 mt-1 text-sm placeholder-gray-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500';
                manualTa.rows = 2;
                manualTa.placeholder = 'Write your own explanation...';
                manualTa.id = 'manual-' + index;
                manualTa.dataset.index = index;
                manualTa.oninput = function () { generatedMoves[parseInt(this.dataset.index, 10)].manual_desc = this.value; };
                manualTa.onclick = function (e) { e.stopPropagation(); };
                card.appendChild(manualTa);

                if (quality === 'blunder') {
                    card.classList.add('border-red-400/30');
                    card.classList.remove('border-white/10');
                }

                cardsContainer.appendChild(card);
            });

            saveSection.classList.remove('hidden');
            goToPly(positions.length - 1);
        }

        form.onsubmit = function () {
            movesDataInput.value = JSON.stringify(generatedMoves);
        };
    })();
    </script>
    @endpush
</x-app-layout>
