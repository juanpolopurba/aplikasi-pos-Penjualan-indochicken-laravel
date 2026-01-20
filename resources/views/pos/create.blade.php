<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('POS - Transaksi Baru') }}
        </h2>
    </x-slot>

    <div class="py-4 md:py-6">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}</div>
            @endif

            <form action="{{ route('pos.store') }}" method="POST" id="posForm">
                @csrf
                <div class="bg-white shadow-sm sm:rounded-lg p-4 md:p-6 border border-gray-100">

                    {{-- Info Kasir & Cabang --}}
                    {{-- Info Kasir & Cabang --}}
                    <div
                        class="mb-6 p-4 border rounded-xl bg-gray-50 flex flex-col md:flex-row md:justify-between gap-4">
                        <div class="grid grid-cols-2 md:block gap-2">
                            <p class="text-xs text-gray-500 font-bold uppercase">Kasir</p>
                            <p class="text-sm font-semibold">{{ Auth::user()->username }}</p>

                            <p class="text-xs text-gray-500 font-bold uppercase mt-2 md:mt-4">Tanggal</p>
                            <p class="text-sm font-semibold">{{ date('d M Y') }}</p>
                        </div>

                        <div class="w-full md:w-64">
                            <label class="text-xs font-bold text-gray-600 uppercase">Cabang:</label>

                            {{-- Izinkan Admin DAN Manager untuk memilih cabang --}}
@if (in_array(Auth::user()->role, ['admin', 'manager']))
    {{-- Dropdown Pilih Cabang --}}
    <select name="cabang_id" id="cabang_id"
        class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-bold text-sm">
        @foreach ($cabangs as $cabang)
            <option value="{{ $cabang->id }}" {{ Auth::user()->cabang_id == $cabang->id ? 'selected' : '' }}>
                {{ $cabang->nama_cabang }}
            </option>
        @endforeach
    </select>
@else
    {{-- Jika Kasir, kunci ke cabangnya sendiri --}}
    <div class="mt-1 p-2 bg-white border rounded-lg flex items-center gap-2 shadow-sm">
        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
        <span class="text-sm font-bold text-gray-800">
            {{ Auth::user()->cabang->nama_cabang ?? 'Cabang Tidak Terdaftar' }}
        </span>
    </div>
    <input type="hidden" name="cabang_id" value="{{ Auth::user()->cabang_id }}">
@endif
                        </div>
                    </div>

                    {{-- Input Area: Responsif --}}
                    <div class="flex flex-col md:flex-row gap-3 mb-6">
                        <div class="flex-grow">
                            <label class="block text-xs font-bold text-gray-500 mb-1 uppercase">Cari Menu</label>
                            <select id="menu-id-select" class="w-full rounded-lg border-gray-300 shadow-sm">
                                <option value="">-- Pilih Menu --</option>
                                @foreach ($menus as $menu)
                                    <option value="{{ $menu->id }}" data-harga="{{ $menu->harga }}">
                                        {{ $menu->nama_menu }} (Rp {{ number_format($menu->harga) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex gap-2 items-end">
                            <div class="w-20 md:w-24">
                                <label
                                    class="block text-xs font-bold text-gray-500 mb-1 uppercase text-center">Qty</label>
                                <input type="number" id="item-qty-input" min="1" value="1"
                                    class="w-full rounded-lg border-gray-300 text-center shadow-sm">
                            </div>
                            <button type="button" id="add-item"
                                class="flex-grow md:flex-none bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg shadow font-bold transition">
                                Tambah
                            </button>
                        </div>
                    </div>

                    {{-- Daftar Pesanan --}}
                    <div class="mb-6">
                        <h3 class="text-sm font-bold text-gray-700 mb-3 uppercase tracking-wider">Detail Pesanan</h3>

                        {{-- Desktop Table --}}
                        <div class="hidden md:block overflow-x-auto border rounded-xl">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500">MENU</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500">HARGA</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500">QTY</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500">SUBTOTAL</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody id="order-items-desktop" class="divide-y divide-gray-100">
                                    {{-- Baris menu masuk sini --}}
                                </tbody>
                            </table>
                        </div>

                        {{-- Mobile Cards --}}
                        <div id="order-items-mobile" class="md:hidden space-y-3">
                            {{-- Kartu menu masuk sini --}}
                        </div>

                        <div id="empty-state" class="text-center py-10 border-2 border-dashed rounded-xl text-gray-400">
                            Belum ada item ditambahkan.
                        </div>
                    </div>

                    {{-- Total & Simpan --}}
                    <div class="border-t pt-6 bg-white sticky bottom-0">
                        <div class="flex justify-between items-center mb-4 px-2">
                            <span class="text-lg font-bold text-gray-600">GRAND TOTAL</span>
                            <span id="grand-total" class="text-3xl font-black text-indigo-700">Rp 0</span>
                        </div>
                        <button type="submit" id="submit-btn" disabled
                            class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-300 text-white font-black py-4 rounded-xl shadow-lg transition-all text-lg">
                            SELESAIKAN TRANSAKSI
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnTambah = document.getElementById('add-item');
            const desktopBody = document.getElementById('order-items-desktop');
            const mobileContainer = document.getElementById('order-items-mobile');
            const displayTotal = document.getElementById('grand-total');
            const emptyState = document.getElementById('empty-state');
            const submitBtn = document.getElementById('submit-btn');

            let itemIndex = 0; // Menggunakan index agar input ganda tidak tertimpa

            btnTambah.addEventListener('click', function() {
                const select = document.getElementById('menu-id-select');
                const qtyInput = document.getElementById('item-qty-input');

                if (!select.value) return alert('Pilih menu!');

                const option = select.options[select.selectedIndex];
                const id = select.value;
                const nama = option.text.split(' (')[0];
                const harga = parseFloat(option.getAttribute('data-harga'));
                const qty = parseInt(qtyInput.value);
                const subtotal = harga * qty;

                // 1. Tambah ke Desktop Table
                const row = document.createElement('tr');
                row.setAttribute('data-index', itemIndex);
                row.innerHTML = `
                    <td class="px-4 py-3 text-sm font-semibold">${nama}<input type="hidden" name="items[${itemIndex}][menu_id]" value="${id}"></td>
                    <td class="px-4 py-3 text-right text-sm">Rp ${harga.toLocaleString()}</td>
                    <td class="px-4 py-3 text-center text-sm">${qty}<input type="hidden" name="items[${itemIndex}][qty]" value="${qty}"></td>
                    <td class="px-4 py-3 text-right font-black text-indigo-600 subtotal-item" data-value="${subtotal}">Rp ${subtotal.toLocaleString()}</td>
                    <td class="px-4 py-3 text-center"><button type="button" class="text-red-500 hover:text-red-700 font-bold hapus-item" onclick="hapusRow(${itemIndex})">Hapus</button></td>
                `;
                desktopBody.appendChild(row);

                // 2. Tambah ke Mobile View (Cards)
                const card = document.createElement('div');
                card.setAttribute('id', `card-${itemIndex}`);
                card.className =
                    "p-4 border rounded-xl bg-white shadow-sm flex justify-between items-center";
                card.innerHTML = `
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-800">${nama}</h4>
                        <p class="text-xs text-gray-500">${qty} x Rp ${harga.toLocaleString()}</p>
                        <p class="text-sm font-black text-indigo-600 mt-1">Rp ${subtotal.toLocaleString()}</p>
                    </div>
                    <button type="button" class="p-2 bg-red-50 text-red-600 rounded-lg" onclick="hapusRow(${itemIndex})">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                `;
                mobileContainer.appendChild(card);

                itemIndex++;
                updateUI();
            });

            window.hapusRow = function(index) {
                document.querySelector(`tr[data-index="${index}"]`).remove();
                document.getElementById(`card-${index}`).remove();
                updateUI();
            }

            function updateUI() {
                let total = 0;
                const items = document.querySelectorAll('.subtotal-item');
                items.forEach(el => {
                    total += parseFloat(el.getAttribute('data-value'));
                });

                displayTotal.innerText = 'Rp ' + total.toLocaleString();

                if (items.length > 0) {
                    emptyState.classList.add('hidden');
                    submitBtn.disabled = false;
                } else {
                    emptyState.classList.remove('hidden');
                    submitBtn.disabled = true;
                }
            }
        });
    </script>
</x-app-layout>
