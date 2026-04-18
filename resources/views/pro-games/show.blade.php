@push('styles')
    <link rel="stylesheet" href="{{ asset('css/chessboard.css') }}">
    <style>
        .move-btn { cursor: pointer; padding: 2px 6px; border-radius: 6px; display: block; white-space: nowrap; }
        .move-btn:hover { background: rgba(255,255,255,0.1); }
        .move-btn.active { background: rgba(16,185,129,0.3); font-weight: 600; color: #6ee7b7; }
        .eval-badge { font-size: 11px; color: #6b7280; font-family: monospace; display: block; }
        #board-wrap { width: 100%; max-width: 500px; margin: 0 auto; }
        #board { width: 100%; }
        @media (min-width: 768px) {
            #board-wrap { width: 500px; max-width: 500px; margin: 0; }
            #board { width: 500px; }
        }
    </style>
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-white leading-tight">{{ $proGame->title ?? 'Untitled Game' }}</h2>
            <div class="flex items-center gap-3">
                @if(auth()->user()->is_admin)
                    <a href="{{ route('pro-games.edit', $proGame) }}" class="text-sm bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-lg hover:bg-yellow-500/30 transition-all duration-200 border border-yellow-500/30">Edit</a>
                    <form method="POST" action="{{ route('pro-games.destroy', $proGame) }}" onsubmit="return confirm('Delete this game?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm bg-red-500/20 text-red-400 px-3 py-1 rounded-lg hover:bg-red-500/30 transition-all duration-200 border border-red-500/30">Delete</button>
                    </form>
                @endif
                <a href="{{ route('pro-games.index') }}" class="text-sm text-emerald-400 hover:text-emerald-300 transition-colors">&larr; Back</a>
            </div>
        </div>
    </x-slot>

    <div class="flex flex-col md:flex-row md:justify-center md:items-start md:gap-4 pt-4 px-2 md:px-4">

        <div class="flex flex-col items-center w-full md:w-auto">

            <div id="review-box" class="mb-3 bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl shadow-xl overflow-hidden w-full" style="max-width:500px;">
                <div id="review-header" class="px-4 py-2 bg-white/5 border-b border-white/5 flex items-center gap-2">
                    <span id="review-dot" class="inline-block w-3 h-3 rounded-full bg-gray-600 flex-shrink-0"></span>
                    <div class="flex-1 min-w-0">
                        <span id="review-title" class="font-bold text-gray-400 text-sm">Select a move</span>
                        <p id="review-move" class="text-xs text-gray-500"></p>
                    </div>
                    <span id="review-score" class="text-xs font-mono text-gray-500 flex-shrink-0"></span>
                </div>
                <div class="px-4 py-3">
                    <p id="review-text" class="text-sm text-gray-300 leading-relaxed">Click on any move to see the analysis.</p>
                </div>
            </div>

            <div id="board-wrap">
                <div id="board" class="rounded-xl overflow-hidden shadow-2xl shadow-black/50 ring-1 ring-white/10"></div>
            </div>

            <div class="flex gap-2 mt-2 justify-center w-full">
                <button type="button" id="btn-first" class="px-3 py-1.5 border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 text-sm font-mono text-gray-300 transition-all duration-200">&lt;&lt;</button>
                <button type="button" id="btn-prev"  class="px-3 py-1.5 border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 text-sm font-mono text-gray-300 transition-all duration-200">&lt;</button>
                <button type="button" id="btn-next"  class="px-3 py-1.5 border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 text-sm font-mono text-gray-300 transition-all duration-200">&gt;</button>
                <button type="button" id="btn-last"  class="px-3 py-1.5 border border-white/10 rounded-lg bg-white/5 hover:bg-white/10 text-sm font-mono text-gray-300 transition-all duration-200">&gt;&gt;</button>
            </div>

            <div class="block md:hidden mt-3 w-full">
                @include('pro-games._move-table')
            </div>

        </div>

        <div class="hidden md:block" style="width:320px; flex-shrink:0;">
            @include('pro-games._move-table')
        </div>

    </div>

    @push('scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/chess.js') }}"></script>
    <script src="{{ asset('js/chessboard.js') }}"></script>
    <script>
    (function () {
        var movesData = @json($proGame->moves_data);

        var chess = new Chess();
        var positions = [chess.fen()];
        for (var i = 0; i < movesData.length; i++) {
            var n = movesData[i].notation || movesData[i].move || '?';
            movesData[i].notation = n;
            chess.move(n, { sloppy: true });
            positions.push(chess.fen());
        }

        var currentPly = 0;

        var boardSize = window.innerWidth >= 768 ? 500 : Math.min(window.innerWidth - 16, 500);
        document.getElementById('board').style.width = boardSize + 'px';
        document.getElementById('board-wrap').style.width = boardSize + 'px';

        var board = Chessboard('board', {
            position: 'start',
            draggable: false,
            pieceTheme: "{{ asset('img/chesspieces/wikipedia') }}/{piece}.png",
        });

        var reviewDot    = document.getElementById('review-dot');
        var reviewTitle  = document.getElementById('review-title');
        var reviewMove   = document.getElementById('review-move');
        var reviewScore  = document.getElementById('review-score');
        var reviewText   = document.getElementById('review-text');
        var reviewHeader = document.getElementById('review-header');

        function goToPly(ply) {
            if (ply < 0) ply = 0;
            if (ply > positions.length - 1) ply = positions.length - 1;
            currentPly = ply;
            board.position(positions[ply].split(' ')[0], true);
            updateReviewBox(ply);
            highlightMoveBtn(ply);
        }

        function updateReviewBox(ply) {
            if (ply === 0) {
                reviewHeader.className = 'px-4 py-2 bg-white/5 border-b border-white/5 flex items-center gap-2';
                reviewDot.className    = 'inline-block w-3 h-3 rounded-full bg-gray-600 flex-shrink-0';
                reviewTitle.textContent = 'Game Start';
                reviewTitle.className  = 'font-bold text-gray-400 text-sm';
                reviewMove.textContent = '';
                reviewScore.textContent = '';
                reviewText.textContent  = 'Click on any move to see the analysis.';
                return;
            }

            var entry    = movesData[ply - 1];
            var moveNum  = Math.ceil(ply / 2);
            var dot      = ply % 2 === 1 ? '.' : '...';
            var moveLabel = moveNum + dot + ' ' + entry.notation;
            var isBrill  = entry.is_brilliant || false;
            var isBlund  = entry.is_blunder || entry.isImportant || false;
            var score    = entry.score;

            if (isBrill) {
                reviewHeader.className = 'px-4 py-2 bg-blue-500/10 border-b border-blue-500/20 flex items-center gap-2';
                reviewDot.className    = 'inline-block w-3 h-3 bg-blue-400 rotate-45 flex-shrink-0';
                reviewTitle.textContent = 'Brilliant Move!!';
                reviewTitle.className  = 'font-bold text-blue-400 text-sm';
            } else if (isBlund) {
                reviewHeader.className = 'px-4 py-2 bg-red-500/10 border-b border-red-500/20 flex items-center gap-2';
                reviewDot.className    = 'inline-block w-3 h-3 rounded-full bg-red-400 flex-shrink-0';
                reviewTitle.textContent = 'Blunder';
                reviewTitle.className  = 'font-bold text-red-400 text-sm';
            } else {
                reviewHeader.className = 'px-4 py-2 bg-emerald-500/10 border-b border-emerald-500/20 flex items-center gap-2';
                reviewDot.className    = 'inline-block w-3 h-3 rounded-full bg-emerald-400 flex-shrink-0';
                reviewTitle.textContent = 'Best Move';
                reviewTitle.className  = 'font-bold text-emerald-400 text-sm';
            }

            reviewMove.textContent  = moveLabel;
            reviewScore.textContent = score !== null && score !== undefined
                ? 'Eval: ' + (score > 0 ? '+' : '') + parseFloat(score).toFixed(1)
                : '';

            var desc = 'No analysis for this move.';
            if (entry.manual_desc && entry.manual_desc.trim()) desc = entry.manual_desc;
            else if (entry.description && entry.description.trim()) desc = entry.description;
            reviewText.textContent = desc;
        }

        function highlightMoveBtn(ply) {
            var btns = document.querySelectorAll('.move-btn');
            for (var i = 0; i < btns.length; i++) {
                btns[i].classList.remove('active');
                if (parseInt(btns[i].dataset.ply, 10) === ply) {
                    btns[i].classList.add('active');
                    btns[i].scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                }
            }
        }

        var moveBtns = document.querySelectorAll('.move-btn');
        for (var j = 0; j < moveBtns.length; j++) {
            moveBtns[j].onclick = function () {
                goToPly(parseInt(this.dataset.ply, 10));
            };
        }

        document.getElementById('btn-first').onclick = function () { goToPly(0); };
        document.getElementById('btn-prev').onclick  = function () { goToPly(currentPly - 1); };
        document.getElementById('btn-next').onclick  = function () { goToPly(currentPly + 1); };
        document.getElementById('btn-last').onclick  = function () { goToPly(positions.length - 1); };

        document.onkeydown = function (e) {
            if (e.key === 'ArrowLeft')  { e.preventDefault(); goToPly(currentPly - 1); }
            if (e.key === 'ArrowRight') { e.preventDefault(); goToPly(currentPly + 1); }
        };
    })();
    </script>
    @endpush
</x-app-layout>
