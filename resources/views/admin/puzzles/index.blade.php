<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-white leading-tight">Puzzles</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-3xl mx-auto">

        <a href="{{ route('admin.puzzles.create') }}"
           class="inline-block mb-6 px-5 py-2.5 bg-gradient-to-r from-emerald-500 to-emerald-700 text-white rounded-xl hover:from-emerald-400 hover:to-emerald-600 text-sm font-medium transition-all duration-200 shadow-lg shadow-emerald-500/20">
            + Add a Puzzle
        </a>

        @if ($puzzles->isEmpty())
            <p class="text-gray-500">No puzzles yet.</p>
        @else
            <div class="space-y-3">
                @foreach ($puzzles as $puzzle)
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl p-4 flex items-center justify-between">
                        <div>
                            <span class="font-medium text-white">{{ $puzzle->title }}</span>
                            <span class="text-gray-500 ml-1">&mdash; {{ $puzzle->difficulty }}</span>
                            @if (is_array($puzzle->solution))
                                <span class="text-gray-500 text-sm ml-1">({{ count($puzzle->solution) }} moves)</span>
                            @endif
                        </div>
                        <div class="flex gap-3 items-center">
                            <a href="{{ route('admin.puzzles.play', $puzzle) }}" class="text-sm text-emerald-400 hover:text-emerald-300 transition-colors">Play</a>
                            <a href="{{ route('admin.puzzles.edit', $puzzle) }}" class="text-sm text-gray-400 hover:text-white transition-colors">Edit</a>
                            <form action="{{ route('admin.puzzles.destroy', $puzzle) }}" method="post">
                                @csrf @method('delete')
                                <button type="submit" class="text-sm text-red-400 hover:text-red-300 transition-colors">Delete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
