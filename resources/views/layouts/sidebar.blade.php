{{-- Sidebar Container --}}
<aside :class="open ? 'translate-x-0' : '-translate-x-full'"
    class="w-64 bg-white border-r border-gray-100 flex flex-col h-full fixed top-0 left-0 z-50 transition-transform duration-300 ease-in-out md:relative md:translate-x-0">

    {{-- LOGO AREA --}}
    <div class="flex items-center justify-between px-4 h-16 shrink-0 border-b border-gray-200">
        <a href="{{ route('dashboard') }}" class="flex items-center space-x-2">
            <x-application-logo class="block h-8 w-auto fill-current text-indigo-600" />
            <span class="text-xl font-bold text-gray-800 uppercase tracking-tight">Indochicken</span>
        </a>

        {{-- Tutup Sidebar (Mobile) --}}
        <button @click="open = false" class="md:hidden text-gray-500 hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    {{-- NAVIGATION LINKS --}}
    <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1">

        {{-- BAGIAN 1: OPERASIONAL --}}
        <div class="px-3 mb-2">
            <h3 class="text-[10px] font-bold uppercase text-gray-400 tracking-widest">Operasional</h3>
        </div>

        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <i class="fas fa-home w-5 mr-3 text-center text-sm"></i>
            {{ __('Dashboard') }}
        </x-nav-link>

        <x-nav-link :href="route('pos.create')" :active="request()->routeIs('pos.create')">
            <i class="fas fa-cash-register w-5 mr-3 text-center text-sm text-green-500"></i>
            {{ __('POS Transaksi') }}
        </x-nav-link>

        <x-nav-link :href="route('pemakaian.create')" :active="request()->routeIs('pemakaian.create')">
            <i class="fas fa-utensils w-5 mr-3 text-center text-sm text-orange-500"></i>
            {{ __('Input Pakai Bahan') }}
        </x-nav-link>

        {{-- BAGIAN 2: LAPORAN & DATA --}}
        <div class="px-3 pt-6 mb-2">
            <h3 class="text-[10px] font-bold uppercase text-gray-400 tracking-widest">Laporan & Data</h3>
        </div>

        <x-nav-link :href="route('laporan.penjualan.index')" :active="request()->routeIs('laporan.penjualan.index')">
            <i class="fas fa-chart-line w-5 mr-3 text-center text-sm"></i>
            {{ __('Laporan Penjualan') }}
        </x-nav-link>

        <x-nav-link :href="route('pemakaian.index')" :active="request()->routeIs('pemakaian.index')">
            <i class="fas fa-history w-5 mr-3 text-center text-sm text-gray-500"></i>
            {{ __('Riwayat Pemakaian') }}
        </x-nav-link>

        {{-- Laba Rugi: Admin & Manager (Owner) --}}
        @if (in_array(Auth::user()->role, ['admin', 'manager']))
            <x-nav-link :href="route('laporan.labarugi')" :active="request()->routeIs('laporan.labarugi')">
                <i class="fas fa-file-invoice-dollar w-5 mr-3 text-center text-sm text-amber-500"></i>
                {{ __('Laba Rugi') }}
            </x-nav-link>
        @endif

        @if (in_array(Auth::user()->role, ['kasir', 'admin']))
            <x-nav-link :href="route('cash_on_hand.index')" :active="request()->routeIs('cash_on_hand.index')">
                <i class="fas fa-wallet mr-2"></i> {{ __('Uang Laci (Cash on Hand)') }}
            </x-nav-link>
        @endif

        @if (in_array(Auth::user()->role, ['admin', 'manager']))
            <x-nav-link :href="route('cash_on_hand.manager')" :active="request()->routeIs('cash_on_hand.manager')">
                <i class="fas fa-chart-line mr-2"></i> {{ __('Monitoring Kas Cabang') }}
            </x-nav-link>
        @endif

        {{-- Menu Khusus Manager (Owner) --}}
        @if (auth()->user()->role === 'manager')
            <div class="px-3 pt-6 mb-2 text-[10px] font-bold uppercase text-orange-500">Owner Menu</div>
            <x-nav-link :href="route('activity-logs.index')" :active="request()->routeIs('activity-logs.index')">
                <i class="fas fa-user-secret w-5 mr-3 text-center text-orange-600"></i>
                {{ __('Pantau Aktivitas Admin') }}
            </x-nav-link>

            <x-nav-link :href="route('user.index')" :active="request()->routeIs('user.*')">
                <i class="fas fa-users w-5 mr-3 text-center text-sm"></i>
                {{ __('Manajemen User') }}
            </x-nav-link>
        @endif

        {{-- Ubah dari @if (auth()->user()->role === 'manager') menjadi: --}}
        @if (in_array(auth()->user()->role, ['manager', 'kasir', 'admin']))
            <x-nav-link :href="route('pengeluaran.index')" :active="request()->routeIs('pengeluaran.*')">
                <i class="fas fa-wallet w-5 mr-3 text-center text-sm text-red-500"></i>
                {{ __('Riwayat Pengeluaran') }}
            </x-nav-link>
        @endif

        <x-nav-link :href="route('pembelian.index')" :active="request()->routeIs('pembelian.*')">
            <i class="fas fa-shopping-cart w-5 mr-3 text-center text-sm"></i>
            {{ __('Pembelian Bahan') }}
        </x-nav-link>

        <x-nav-link :href="route('bahan_baku.index')" :active="request()->routeIs('bahan_baku.*')">
            <i class="fas fa-boxes w-5 mr-3 text-center text-sm text-blue-500"></i>
            {{ __('Stok Bahan Baku') }}
        </x-nav-link>

        {{-- BAGIAN 3: ADMINISTRASI & MASTER DATA --}}
        @if (in_array(Auth::user()->role, ['admin', 'manager']))
            <div class="px-3 pt-6 mb-2">
                <h3 class="text-[10px] font-bold uppercase text-gray-400 tracking-widest">Administrasi</h3>
            </div>

            <x-nav-link :href="route('menu.index')" :active="request()->routeIs('menu.*')">
                <i class="fas fa-mug-hot w-5 mr-3 text-center text-sm text-indigo-500"></i>
                {{ __('Data Menu & Harga') }}
            </x-nav-link>

            {{-- Khusus Fitur Teknis (Hanya Admin) --}}
            @if (Auth::user()->role === 'admin')
                <x-nav-link :href="route('cabang.index')" :active="request()->routeIs('cabang.*')">
                    <i class="fas fa-building w-5 mr-3 text-center text-sm"></i>
                    {{ __('Data Cabang') }}
                </x-nav-link>

                <x-nav-link :href="route('kategori.index')" :active="request()->routeIs('kategori.*')">
                    <i class="fas fa-tags w-5 mr-3 text-center text-sm"></i>
                    {{ __('Kategori Biaya') }}
                </x-nav-link>
            @endif
        @endif
    </nav>

    {{-- FOOTER PROFILE MINI --}}
    <div class="mt-auto px-6 py-4 border-t border-gray-100 bg-gray-50/50">
        <div class="flex items-center">
            <div class="shrink-0 mr-3">
                <div
                    class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            </div>
            <div class="overflow-hidden">
                <div class="text-sm font-bold text-gray-800 truncate">{{ Auth::user()->name }}</div>
                <div class="text-[10px] text-gray-500 uppercase tracking-widest font-medium">
                    {{ Auth::user()->cabang->nama_cabang ?? 'Pusat' }}
                </div>
            </div>
        </div>
    </div>
</aside>
