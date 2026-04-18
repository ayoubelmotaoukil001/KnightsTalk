@push('styles')
    <link rel="stylesheet" href="{{ asset('css/chessboard.css') }}">
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-white leading-tight">{{ $lesson->title }}</h2>
            <a href="{{ route('courses.show', $course) }}" class="text-sm text-emerald-400 hover:text-emerald-300 transition-colors">&larr; {{ $course->title }}</a>
        </div>
    </x-slot>

    <div class="flex flex-col items-center pt-6 pb-10 px-3">

        <div id="review-box" class="mb-4 w-full bg-blue-500/10 border border-blue-500/20 rounded-2xl overflow-hidden backdrop-blur-md" style="max-width:500px">
            <div class="px-4 py-2 bg-blue-500/10 border-b border-blue-500/20">
                <span id="review-move" class="font-bold text-blue-400 text-sm">Click Next to begin</span>
            </div>
            <div class="px-4 py-3 min-h-[56px]">
                <p id="review-text" class="text-sm text-gray-300">This lesson has {{ count($lesson->move_descriptions ?? []) }} moves.</p>
            </div>
        </div>

        <div id="board" style="width:500px" class="rounded-xl overflow-hidden shadow-2xl shadow-black/50 ring-1 ring-white/10"></div>

        <p id="move-counter" class="mt-2 text-xs text-gray-500">Move 0 / {{ count($lesson->move_descriptions ?? []) }}</p>

        <div class="flex gap-4 mt-4 w-full" style="max-width:500px">
            <button id="btn-prev" class="flex-1 py-3 bg-white/5 border border-white/10 text-gray-300 text-base font-semibold rounded-xl hover:bg-white/10 transition-all duration-200">&larr; Previous</button>
            <button id="btn-next" class="flex-1 py-3 bg-gradient-to-r from-emerald-500 to-emerald-700 text-white text-base font-semibold rounded-xl hover:from-emerald-400 hover:to-emerald-600 transition-all duration-200 shadow-lg shadow-emerald-500/20">Next &rarr;</button>
        </div>

        <div id="complete-msg" class="hidden mt-5 w-full p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl text-center" style="max-width:500px">
            <p class="text-emerald-400 font-semibold text-lg">Lesson Complete!</p>
        </div>

        <div class="mt-4 w-full" style="max-width:500px">
            @if($isCompleted)
                <button id="btn-complete" disabled class="w-full py-3 bg-emerald-500/20 text-emerald-400 text-base font-semibold rounded-xl opacity-80 cursor-default border border-emerald-500/20">Already Completed</button>
            @else
                <button id="btn-complete" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-emerald-700 text-white text-base font-semibold rounded-xl hover:from-emerald-400 hover:to-emerald-600 transition-all duration-200 shadow-lg shadow-emerald-500/20">Mark Lesson as Completed</button>
            @endif
        </div>

        @if($nextLesson)
            <a href="{{ route('courses.lesson', [$course, $nextLesson]) }}" id="btn-next-lesson"
               class="{{ $isCompleted ? '' : 'hidden' }} mt-3 w-full text-center py-3 bg-blue-600 text-white rounded-xl text-base font-semibold hover:bg-blue-500 transition-all duration-200" style="max-width:500px">
                Next Lesson &rarr;
            </a>
        @else
            <a href="{{ route('courses.show', $course) }}" id="btn-next-lesson"
               class="{{ $isCompleted ? '' : 'hidden' }} mt-3 w-full text-center py-3 bg-blue-600 text-white rounded-xl text-base font-semibold hover:bg-blue-500 transition-all duration-200" style="max-width:500px">
                Back to Course
            </a>
        @endif
    </div>

    @push('scripts')
    <script src="{{ asset('js/jquery.min.js') }}"></script>
    <script src="{{ asset('js/chess.js') }}"></script>
    <script src="{{ asset('js/chessboard.js') }}"></script>
    <script>
    (function () {
        var movesData  = @json($lesson->move_descriptions ?? []);
        var totalMoves = movesData.length;
        var currentPly = 0;

        var chess = new Chess();
        var positions = [chess.fen()];
        for (var i = 0; i < movesData.length; i++) {
            chess.move(movesData[i].notation, { sloppy: true });
            positions.push(chess.fen());
        }

        var board = Chessboard('board', {
            position: 'start',
            draggable: false,
            pieceTheme: "{{ asset('img/chesspieces/wikipedia') }}/{piece}.png",
        });

        var reviewMove  = document.getElementById('review-move');
        var reviewText  = document.getElementById('review-text');
        var moveCounter = document.getElementById('move-counter');
        var completeMsg = document.getElementById('complete-msg');
        var nextBtn     = document.getElementById('btn-next-lesson');

        function goToPly(ply) {
            if (ply < 0) ply = 0;
            if (ply > positions.length - 1) ply = positions.length - 1;
            currentPly = ply;
            board.position(positions[ply].split(' ')[0], true);

            moveCounter.textContent = 'Move ' + ply + ' / ' + totalMoves;

            if (ply === 0) {
                reviewMove.textContent = 'Click Next to begin';
                reviewText.textContent = 'This lesson has ' + totalMoves + ' moves.';
                completeMsg.classList.add('hidden');
                return;
            }

            var entry = movesData[ply - 1];
            var num   = Math.ceil(ply / 2);
            var dot   = ply % 2 === 1 ? '.' : '...';
            reviewMove.textContent = num + dot + ' ' + entry.notation;
            reviewText.textContent = entry.description && entry.description.trim() ? entry.description : '';

            if (ply >= totalMoves) { completeMsg.classList.remove('hidden'); }
            else                   { completeMsg.classList.add('hidden'); }
        }

        document.getElementById('btn-prev').onclick = function () { goToPly(currentPly - 1); };
        document.getElementById('btn-next').onclick = function () { goToPly(currentPly + 1); };
        document.onkeydown = function (e) {
            if (e.key === 'ArrowLeft')  goToPly(currentPly - 1);
            if (e.key === 'ArrowRight') goToPly(currentPly + 1);
        };

        goToPly(0);

        var btnComplete = document.getElementById('btn-complete');
        if (btnComplete && !btnComplete.disabled) {
            btnComplete.onclick = function () {
                btnComplete.disabled = true;
                btnComplete.textContent = 'Saving...';
                fetch("{{ route('courses.lesson.complete', [$course, $lesson]) }}", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: '{}'
                }).then(function (r) { return r.json(); }).then(function () {
                    btnComplete.textContent = 'Completed!';
                    btnComplete.className = 'w-full py-3 bg-emerald-500/20 text-emerald-400 text-base font-semibold rounded-xl opacity-80 cursor-default border border-emerald-500/20';
                    if (nextBtn) nextBtn.classList.remove('hidden');
                });
            };
        }
    })();
    </script>
    @endpush
</x-app-layout>
