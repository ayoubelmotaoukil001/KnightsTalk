<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Puzzles</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4">

        @if ($puzzles->isEmpty())
            <p class="text-gray-500">No puzzles yet. Check back later!</p>
        @else
            <div class="space-y-3">
                @foreach ($puzzles as $puzzle)
                <div class="bg-white shadow-sm rounded-lg p-4 flex items-center justify-between">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $puzzle->title }}</p>
                        <p class="text-sm text-gray-500">Difficulty: {{ $puzzle->difficulty }}</p>
                    </div>
                    <a href="{{ route('puzzles.play', $puzzle) }}"
                       class="bg-indigo-500 hover:bg-indigo-600 text-white text-sm px-4 py-2 rounded-lg">
                        Play
                    </a>
                </div>
                @endforeach
            </div>
        @endif

    </div>
</x-app-layout>
