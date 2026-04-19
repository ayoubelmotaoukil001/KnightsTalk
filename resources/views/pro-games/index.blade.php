<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-sm text-slate-600 leading-tight tracking-widest uppercase dark:text-slate-500">Pro Games</h2>
            @if(auth()->user()->is_admin)
                <a href="{{ route('pro-games.create') }}"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-amber-500/40 bg-transparent px-3.5 py-1.5 text-xs font-semibold text-amber-700 transition-all duration-300 hover:border-amber-500 hover:bg-amber-500 hover:text-white hover:shadow-md dark:border-amber-500/30 dark:text-amber-400 dark:hover:shadow-[0_0_20px_rgba(245,158,11,0.25)]">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    New Game
                </a>
            @endif
        </div>
    </x-slot>

    <div class="mx-auto max-w-5xl px-4 py-8">

        @if (session('success'))
            <div class="mb-5 rounded-2xl border border-amber-200/80 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-500/20 dark:bg-amber-500/[0.06] dark:text-amber-400">
                {{ session('success') }}
            </div>
        @endif

        @if($games->isEmpty())
            <div class="py-24 text-center">
                <p class="mb-4 select-none text-4xl text-slate-300 dark:text-slate-600">♛</p>
                <p class="text-slate-500 dark:text-slate-600">No pro games have been added yet.</p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($games as $game)
                    <div class="group rounded-3xl border border-slate-200/90 bg-white p-5 shadow-sm transition-all duration-500 hover:border-red-300 hover:shadow-md dark:border-white/[0.07] dark:bg-white/[0.03] dark:shadow-none dark:hover:border-red-500/20 dark:hover:bg-white/[0.05] dark:hover:shadow-2xl dark:hover:shadow-black/50">

                        <a href="{{ route('pro-games.show', $game) }}" class="block">
                            <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200/90 bg-slate-50 transition-all duration-300 group-hover:border-amber-300 group-hover:bg-amber-50 dark:border-white/[0.07] dark:bg-white/[0.04] dark:group-hover:border-amber-500/20 dark:group-hover:bg-amber-500/[0.06]">
                                <span class="select-none text-lg leading-none text-slate-400 transition-colors duration-300 group-hover:text-amber-600 dark:text-slate-600 dark:group-hover:text-amber-400/70">♛</span>
                            </div>

                            <h3 class="mb-1 text-base font-semibold leading-snug text-slate-900 transition-colors duration-300 group-hover:text-red-600 dark:text-white dark:group-hover:text-red-300">
                                {{ $game->title ?? 'Untitled Game' }}
                            </h3>
                            <p class="mb-4 text-xs text-slate-500 dark:text-slate-600">
                                {{ count($game->moves_data ?? []) }} moves &middot; {{ $game->created_at->diffForHumans() }}
                            </p>
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-red-600 transition-colors duration-200 group-hover:text-red-500 dark:text-red-500/60 dark:group-hover:text-red-400">
                                View analysis
                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </a>

                        @if(auth()->user()->is_admin)
                            <div class="mt-4 flex gap-2 border-t border-slate-200/80 pt-3 dark:border-white/[0.05]">
                                <a href="{{ route('pro-games.edit', $game) }}"
                                   class="rounded-lg border border-amber-200/80 bg-amber-50/50 px-2.5 py-1 text-[11px] font-medium text-amber-800 transition-all duration-300 hover:border-amber-400 dark:border-amber-500/20 dark:bg-transparent dark:text-amber-500/70 dark:hover:border-amber-500/40 dark:hover:bg-amber-500/10 dark:hover:text-amber-400">Edit</a>
                                <form method="POST" action="{{ route('pro-games.destroy', $game) }}" onsubmit="return confirm('Delete this game?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200/80 bg-red-50/50 px-2.5 py-1 text-[11px] font-medium text-red-700 transition-all duration-300 hover:border-red-400 dark:border-red-500/20 dark:bg-transparent dark:text-red-500/60 dark:hover:border-red-500/40 dark:hover:bg-red-500/10 dark:hover:text-red-400">Delete</button>
                                </form>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        @endif

    </div>
</x-app-layout>
