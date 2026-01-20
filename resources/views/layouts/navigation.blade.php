{{-- Hapus x-data="{ open: false }" di sini karena sudah ada di app.blade.php (body) --}}
<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                {{-- TOMBOL HAMBURGER UNTUK SIDEBAR (TAMBAHAN) --}}
                <div class="flex items-center md:hidden mr-4">
                    <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                        <i class="fas fa-bars text-xl"></i> {{-- Gunakan Icon FontAwesome agar lebih jelas --}}
                    </button>
                </div>

                {{-- <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <span class="font-bold text-red-600 text-xl">Indochicken</span>
                    </a>
                </div>

                {{-- Link desktop tetap ada --}}
                {{-- <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div> --}} 
            </div>

            {{-- Bagian Kanan (Desktop View) --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
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
                                        onclick="event.preventDefault();
                                                 this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>

            {{-- Tombol Dropdown Profil Mobile (Bawaan) --}}
            {{-- Kita ganti nama variabel agar tidak bentrok dengan sidebar --}}
            <div x-data="{ profileOpen: false }" class="-me-2 flex items-center sm:hidden">
                <button @click="profileOpen = ! profileOpen" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': profileOpen, 'inline-flex': ! profileOpen }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        <path :class="{'hidden': ! profileOpen, 'inline-flex': profileOpen }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                {{-- Dropdown Profile Mobile --}}
                <div x-show="profileOpen" @click.away="profileOpen = false" class="absolute right-0 top-16 w-48 bg-white shadow-xl py-2 z-50">
                     <x-responsive-nav-link :href="route('profile.edit')">Profile</x-responsive-nav-link>
                     <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">Log Out</x-responsive-nav-link>
                     </form>
                </div>
            </div>
        </div>
    </div>
</nav>