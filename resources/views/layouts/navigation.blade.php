<nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/80 backdrop-blur-xl dark:border-white/[0.05] dark:bg-[rgba(5,5,5,0.85)] dark:backdrop-blur-xl">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center" style="height:62px;">

            {{-- Logo + links --}}
            <div class="flex items-center gap-7">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/20 flex items-center justify-center group-hover:bg-amber-500/20 transition-all duration-300">
                        <svg class="w-4 h-4 text-amber-500 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-1 0-2.5 1-2.5 1L8 6.5S6.5 7 6 8c-.5 1-.5 2-.5 2L4 12h3l-1 6h12l-1-6h3l-1.5-2s0-1-.5-2c-.5-1-2-1.5-2-1.5L14.5 4S13 3 12 3z"/>
                            <rect x="7" y="18" width="10" height="2" rx="1"/>
                        </svg>
                    </div>
                    <span class="text-base font-bold text-slate-900 hidden sm:block dark:text-white" style="font-family:'Playfair Display',serif; letter-spacing:-0.01em;">KnightsTalk</span>
                </a>

                <span class="hidden sm:block h-4 w-px bg-slate-200 dark:bg-white/[0.08]"></span>

                <div class="hidden sm:flex items-center gap-0.5">
                    <x-nav-link :href="route('dashboard')"       :active="request()->routeIs('dashboard')">Dashboard</x-nav-link>
                    <x-nav-link :href="route('courses.index')"   :active="request()->routeIs('courses.*')">Courses</x-nav-link>
                    <x-nav-link :href="route('pro-games.index')" :active="request()->routeIs('pro-games.*')">Pro Games</x-nav-link>
                    <x-nav-link :href="route('puzzles.index')"   :active="request()->routeIs('puzzles.*')">Puzzles</x-nav-link>
                    <x-nav-link :href="route('chat.index')"      :active="request()->routeIs('chat.*')">Chat</x-nav-link>
                    @if(auth()->user()->is_admin)
                        <span class="h-3 w-px bg-slate-200 mx-1.5 dark:bg-white/[0.08]"></span>
                        <x-nav-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.*')">
                            <span class="text-amber-600 dark:text-amber-400/80">Manage</span>
                        </x-nav-link>
                        <x-nav-link :href="route('pro-games.create')" :active="request()->routeIs('pro-games.create')">
                            <span class="text-amber-600 dark:text-amber-400/80">+ Game</span>
                        </x-nav-link>
                    @endif
                </div>
            </div>

            {{-- Right: theme + user --}}
            <div class="hidden sm:flex items-center gap-2">
                <button type="button" onclick="window.ktToggleTheme()" title="Toggle light / dark mode"
                        class="p-2 rounded-xl border border-slate-200/90 text-slate-600 hover:bg-slate-100 hover:text-slate-900 transition-all duration-300 dark:border-white/[0.08] dark:text-slate-400 dark:hover:bg-white/[0.06] dark:hover:text-white">
                    <span class="block dark:hidden" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                        </svg>
                    </span>
                    <span class="hidden dark:block" aria-hidden="true">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </span>
                </button>

                <x-dropdown align="right" width="48" contentClasses="py-1 bg-white border border-slate-200 rounded-xl shadow-xl dark:bg-[#0d0d0d] dark:border-white/[0.08] dark:shadow-black/40">
                    <x-slot name="trigger">
                        <button class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl text-sm text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200/90 transition-all duration-300 dark:text-slate-400 dark:hover:text-white dark:bg-white/[0.03] dark:hover:bg-white/[0.06] dark:border-white/[0.07] dark:hover:border-white/[0.12]">
                            <div class="w-6 h-6 rounded-full bg-amber-500/15 border border-amber-500/25 flex items-center justify-center text-[10px] font-bold text-amber-600 dark:text-amber-400">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="font-medium">{{ Auth::user()->name }}</span>
                            <svg class="w-3 h-3 text-slate-400 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">Profile</x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                Log Out
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Mobile: theme + hamburger --}}
            <div class="flex items-center gap-1 sm:hidden">
                <button type="button" onclick="window.ktToggleTheme()" title="Toggle theme"
                        class="p-2 rounded-lg border border-slate-200/90 text-slate-600 dark:border-white/[0.08] dark:text-slate-400">
                    <span class="block dark:hidden"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg></span>
                    <span class="hidden dark:block"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg></span>
                </button>
                <button @click="open = ! open" class="p-2 rounded-lg text-slate-600 hover:text-slate-900 hover:bg-slate-100 dark:text-slate-500 dark:hover:text-white dark:hover:bg-white/[0.05] transition-all duration-200">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': !open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': !open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div :class="{'block': open, 'hidden': !open}" class="hidden sm:hidden border-t border-slate-200/80 bg-white/95 backdrop-blur-xl dark:border-white/[0.05] dark:bg-[rgba(5,5,5,0.97)] dark:backdrop-blur-xl">
        <div class="px-4 pt-3 pb-2 space-y-0.5">
            <x-responsive-nav-link :href="route('dashboard')"       :active="request()->routeIs('dashboard')">Dashboard</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('courses.index')"   :active="request()->routeIs('courses.*')">Courses</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pro-games.index')" :active="request()->routeIs('pro-games.*')">Pro Games</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('puzzles.index')"   :active="request()->routeIs('puzzles.*')">Puzzles</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('chat.index')"      :active="request()->routeIs('chat.*')">Chat</x-responsive-nav-link>
            @if(auth()->user()->is_admin)
                <x-responsive-nav-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.*')">Manage Courses</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('pro-games.create')"    :active="request()->routeIs('pro-games.create')">+ New Game</x-responsive-nav-link>
            @endif
        </div>
        <div class="px-4 pt-3 pb-4 border-t border-slate-200/80 dark:border-white/[0.05]">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-amber-500/15 border border-amber-500/25 flex items-center justify-center text-sm font-bold text-amber-600 dark:text-amber-400">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div>
                    <div class="text-sm font-semibold text-slate-900 dark:text-white">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-slate-500 dark:text-slate-600">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="space-y-0.5">
                <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
