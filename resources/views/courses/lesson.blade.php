@push('styles')
    <link rel="stylesheet" href="{{ asset('css/chessboard.css') }}">
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-bold leading-tight text-slate-900 dark:text-white">{{ $lesson->title }}</h2>
            <a href="{{ route('courses.show', $course) }}" class="text-sm font-medium text-red-600 transition-colors hover:text-red-500 dark:text-red-400 dark:hover:text-red-300">
                &larr; {{ $course->title }}
            </a>
        </div>
    </x-slot>

    <div class="flex flex-col items-center px-3 pb-10 pt-6">

        <div id="review-box" class="mb-4 w-full max-w-[500px] overflow-hidden rounded-2xl border border-blue-200/90 bg-blue-50/80 shadow-sm backdrop-blur-md dark:border-blue-500/20 dark:bg-blue-500/10 dark:shadow-none">
            <div class="border-b border-blue-200/80 bg-blue-100/50 px-4 py-2 dark:border-blue-500/20 dark:bg-blue-500/10">
                <span id="review-move" class="text-sm font-bold text-blue-800 dark:text-blue-400">Click Next to begin</span>
            </div>
            <div class="min-h-[56px] px-4 py-3">
                <p id="review-text" class="text-sm text-slate-700 dark:text-gray-300">This lesson has {{ count($lesson->move_descriptions ?? []) }} moves.</p>
            </div>
        </div>

        <div id="board" style="width:500px;max-width:100%" class="overflow-hidden rounded-xl shadow-lg ring-1 ring-slate-200 dark:shadow-2xl dark:shadow-black/50 dark:ring-white/10"></div>

        <p id="move-counter" class="mt-2 text-xs text-slate-600 dark:text-gray-500">Move 0 / {{ count($lesson->move_descriptions ?? []) }}</p>

        <div class="mt-4 flex w-full max-w-[500px] gap-4">
            <button type="button" id="btn-prev" class="flex-1 rounded-xl border border-slate-200/90 bg-white py-3 text-base font-semibold text-slate-700 transition-all duration-200 hover:bg-slate-50 dark:border-white/10 dark:bg-white/5 dark:text-gray-300 dark:hover:bg-white/10">&larr; Previous</button>
            <button type="button" id="btn-next" class="flex-1 rounded-xl border border-red-500/50 bg-gradient-to-b from-red-500 to-red-600 py-3 text-base font-semibold text-white shadow-md transition-all duration-200 hover:from-red-400 hover:to-red-500 dark:shadow-lg dark:shadow-red-500/20">Next &rarr;</button>
        </div>

        <div id="complete-msg" class="mt-5 hidden w-full max-w-[500px] rounded-2xl border border-emerald-200/90 bg-emerald-50 p-4 text-center dark:border-emerald-500/20 dark:bg-emerald-500/10">
            <p class="text-lg font-semibold text-emerald-800 dark:text-emerald-400">Lesson Complete!</p>
        </div>

        <div class="mt-4 w-full max-w-[500px]">
            @if($isCompleted)
                <button type="button" id="btn-complete" disabled class="w-full cursor-default rounded-xl border border-emerald-200/90 bg-emerald-50 py-3 text-base font-semibold text-emerald-800 opacity-90 dark:border-emerald-500/20 dark:bg-emerald-500/20 dark:text-emerald-400">Already Completed</button>
            @else
                <button type="button" id="btn-complete" class="w-full rounded-xl border border-red-500/50 bg-gradient-to-b from-red-500 to-red-600 py-3 text-base font-semibold text-white shadow-md transition-all duration-200 hover:from-red-400 hover:to-red-500 dark:shadow-lg dark:shadow-red-500/20">Mark Lesson as Completed</button>
            @endif
        </div>

        @if($nextLesson)
            <a href="{{ route('courses.lesson', [$course, $nextLesson]) }}" id="btn-next-lesson"
               class="{{ $isCompleted ? '' : 'hidden' }} mt-3 w-full max-w-[500px] rounded-xl bg-blue-600 py-3 text-center text-base font-semibold text-white transition-all duration-200 hover:bg-blue-500">
                Next Lesson &rarr;
            </a>
        @else
            <a href="{{ route('courses.show', $course) }}" id="btn-next-lesson"
               class="{{ $isCompleted ? '' : 'hidden' }} mt-3 w-full max-w-[500px] rounded-xl bg-blue-600 py-3 text-center text-base font-semibold text-white transition-all duration-200 hover:bg-blue-500">
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
                    btnComplete.className = 'w-full cursor-default rounded-xl border border-emerald-200/90 bg-emerald-50 py-3 text-base font-semibold text-emerald-800 opacity-90 dark:border-emerald-500/20 dark:bg-emerald-500/20 dark:text-emerald-400';
                    if (nextBtn) nextBtn.classList.remove('hidden');
                });
            };
        }
    })();
    </script>
    @endpush
</x-app-layout>
