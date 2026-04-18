<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-white leading-tight">Pro Games</h2>
            @if(auth()->user()->is_admin)
                <a href="{{ route('pro-games.create') }}"
                    class="bg-gradient-to-r from-emerald-500 to-emerald-700 text-white px-4 py-2 rounded-xl text-sm hover:from-emerald-400 hover:to-emerald-600 font-medium transition-all duration-200 shadow-lg shadow-emerald-500/20">
                    + New Pro Game
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8 px-4 max-w-5xl mx-auto">
        @if (session('success'))
            <div class="mb-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
        @endif

        @if($games->isEmpty())
            <p class="text-gray-500">No pro games have been added yet.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($games as $game)
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl hover:bg-white/10 hover:border-white/20 transition-all duration-200 p-5">
                        <a href="{{ route('pro-games.show', $game) }}">
                            <h3 class="font-bold text-white text-lg mb-1">{{ $game->title ?? 'Untitled Game' }}</h3>
                            <p class="text-sm text-gray-500">
                                {{ count($game->moves_data ?? []) }} moves &middot;
                                {{ $game->created_at->diffForHumans() }}
                            </p>
                            <span class="mt-2 inline-block text-sm text-emerald-400 hover:text-emerald-300 transition-colors">View game &rarr;</span>
                        </a>
                        @if(auth()->user()->is_admin)
                            <div class="mt-3 pt-3 border-t border-white/5 flex gap-2">
                                <a href="{{ route('pro-games.edit', $game) }}"
                                    class="text-xs bg-yellow-500/20 text-yellow-400 px-3 py-1 rounded-lg hover:bg-yellow-500/30 transition-all duration-200 border border-yellow-500/30">Edit</a>
                                <form method="POST" action="{{ route('pro-games.destroy', $game) }}" onsubmit="return confirm('Delete this game?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs bg-red-500/20 text-red-400 px-3 py-1 rounded-lg hover:bg-red-500/30 transition-all duration-200 border border-red-500/30">Delete</button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>
