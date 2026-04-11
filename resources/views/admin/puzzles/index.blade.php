<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Puzzles</h2>
    </x-slot>

    <div class="py-8 px-4 max-w-3xl mx-auto">
        @if (session('success'))
            <p style="color: green; margin-bottom: 1rem;">{{ session('success') }}</p>
        @endif

        <p style="margin-bottom: 1rem;">
            <a href="{{ route('admin.puzzles.create') }}" style="text-decoration: underline;">Add a puzzle</a>
        </p>

        @if ($puzzles->isEmpty())
            <p>No puzzles yet.</p>
        @else
            @foreach ($puzzles as $puzzle)
                <div style="border: 1px solid #ccc; padding: 1rem; margin-bottom: 0.5rem;">
                    <strong>{{ $puzzle->title }}</strong>
                    — {{ $puzzle->difficulty }}
                    @if (is_array($puzzle->solution))
                        ({{ count($puzzle->solution) }} moves)
                    @endif
                    <form action="{{ route('admin.puzzles.destroy', $puzzle) }}" method="post" style="display: inline; margin-left: 1rem;">
                        @csrf
                        @method('delete')
                        <button type="submit">Delete</button>
                    </form>
                </div>
            @endforeach
        @endif
    </div>
</x-app-layout>
