<nav x-data="{ open: false }" class="bg-[#0f1219] border-b border-white/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <svg class="w-7 h-7 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c-1 0-2.5 1-2.5 1L8 6.5S6.5 7 6 8c-.5 1-.5 2-.5 2L4 12h3l-1 6h12l-1-6h3l-1.5-2s0-1-.5-2c-.5-1-2-1.5-2-1.5L14.5 4S13 3 12 3z"/>
                            <rect x="7" y="18" width="10" height="2" rx="1"/>
                        </svg>
                        <span class="text-lg font-bold text-white tracking-tight hidden sm:block">KnightsTalk</span>
                    </a>
                </div>

                <div class="hidden space-x-1 sm:-my-px sm:ms-8 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')">
                        {{ __('Courses') }}
                    </x-nav-link>
                    <x-nav-link :href="route('pro-games.index')" :active="request()->routeIs('pro-games.*')">
                        {{ __('Pro Games') }}
                    </x-nav-link>
                    @if(auth()->user()->is_admin)
                        <x-nav-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*')">
                            {{ __('Manage Courses') }}
                        </x-nav-link>
                        <x-nav-link :href="route('pro-games.create')" :active="request()->routeIs('pro-games.create')">
                            {{ __('+ New Pro Game') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg text-gray-400 hover:text-white bg-white/5 hover:bg-white/10 border border-white/5 transition-all duration-200">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 transition-all duration-200">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-white/5">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')">
                {{ __('Courses') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('pro-games.index')" :active="request()->routeIs('pro-games.*')">
                {{ __('Pro Games') }}
            </x-responsive-nav-link>
            @if(auth()->user()->is_admin)
                <x-responsive-nav-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*')">
                    {{ __('Manage Courses') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('pro-games.create')" :active="request()->routeIs('pro-games.create')">
                    {{ __('+ New Pro Game') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-white/5">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
