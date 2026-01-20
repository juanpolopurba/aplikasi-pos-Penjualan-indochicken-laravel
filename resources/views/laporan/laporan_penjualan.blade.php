<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Penjualan</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- SECTION FILTER --}}
        <div class="mb-6 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
            <form action="{{ route('laporan.penjualan.index') }}" method="GET"
                class="grid grid-cols-2 md:grid-cols-5 gap-4 items-end">

                {{-- Cabang --}}
                @if (in_array(auth()->user()->role, ['admin', 'manager']))
                    <div class="col-span-2 md:col-span-1">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Cabang</label>
                        <select name="cabang_id"
                            class="w-full border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-xl text-xs shadow-sm">
                            <option value="">Semua Cabang</option>
                            @foreach ($cabangs as $cabang)
                                <option value="{{ $cabang->id }}"
                                    {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                    {{ $cabang->nama_cabang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                {{-- Dari Tanggal --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Dari</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                        class="w-full border-gray-200 rounded-xl text-xs">
                </div>

                {{-- Sampai Tanggal --}}
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-1 ml-1">Sampai</label>
                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                        class="w-full border-gray-200 rounded-xl text-xs">
                </div>

                <div class="col-span-2 md:col-span-2 flex gap-2">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white py-2 rounded-xl text-xs font-bold uppercase shadow-md hover:bg-indigo-700">
                        Filter
                    </button>

                    {{-- Tombol Rekap PDF --}}
                    <a href="{{ route('laporan.penjualan.rekap_pdf', [
                        'start_date' => request('start_date'),
                        'end_date' => request('end_date'),
                        'cabang_id' => request('cabang_id'),
                    ]) }}"
                        target="_blank"
                        class="flex-1 bg-red-500 text-white py-2 rounded-xl text-xs font-bold uppercase text-center shadow-md hover:bg-red-600 transition-colors">
                        Rekap PDF
                    </a>
                </div>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="flex flex-col gap-3">
            {{-- Header Desktop --}}
            <div class="hidden md:grid grid-cols-12 gap-4 px-4 py-2 text-[10px] font-bold text-gray-400 uppercase">
                <div class="col-span-2">ID & Cabang</div>
                <div class="col-span-2 text-center">Petugas</div>
                <div class="col-span-2 text-center">Waktu Transaksi</div>
                <div class="col-span-3 text-right">Total</div>
                <div class="col-span-3 text-right">Aksi</div>
            </div>

            @forelse ($laporans as $laporan)
                <div class="bg-white border border-gray-100 rounded-xl p-4 hover:shadow-md transition-all">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                        {{-- ID & Cabang --}}
                        <div class="md:col-span-2">
                            <span class="text-[10px] font-bold text-indigo-500">#{{ $laporan->id }}</span><br>
                            <span class="text-sm font-black">{{ $laporan->cabang->nama_cabang ?? 'Pusat' }}</span>
                        </div>

                        {{-- KOLOM PETUGAS --}}
                        <div class="md:col-span-2 text-center">
                            <span
                                class="text-[10px] font-bold text-gray-400 uppercase block leading-none">Petugas:</span>
                            <span class="text-xs font-bold text-gray-700">{{ $laporan->user->name ?? 'System' }}</span>
                        </div>

                        {{-- KOLOM TANGGAL & JAM --}}
                        <div class="md:col-span-2 text-center">
                            <div class="text-xs font-bold text-gray-800">
                                {{ \Carbon\Carbon::parse($laporan->tanggal)->format('d/m/Y') }}</div>
                            <div class="text-[10px] text-indigo-500">{{ $laporan->created_at->format('H:i') }} WIB
                            </div>
                        </div>

                        {{-- TOTAL --}}
                        <div class="md:col-span-3 text-right font-black text-indigo-600">
                            Rp{{ number_format($laporan->total_penjualan, 0, ',', '.') }}
                        </div>

                        {{-- AKSI --}}
                        <div class="md:col-span-3 flex justify-end gap-2">
                            <a href="{{ route('laporan.penjualan.pdf', $laporan->id) }}" target="_blank"
                                class="bg-emerald-50 text-emerald-600 px-3 py-1.5 rounded-lg text-[10px] font-bold border border-emerald-100">PDF</a>
                            <button onclick="bukaModal({{ $laporan->id }})"
                                class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg text-[10px] font-bold uppercase">Detail</button>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center py-10 text-gray-400">Data tidak ditemukan</p>
            @endforelse
        </div>

        {{-- MODAL --}}
        <div id="modalLaporan" class="fixed inset-0 z-50 hidden flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" onclick="tutupModal()"></div>
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[400px] z-10 overflow-hidden relative">
                <div class="p-4 border-b flex justify-between bg-white items-center">
                    <h4 class="font-black text-sm uppercase text-gray-700">Nota #<span id="md-id"></span></h4>
                    <button onclick="tutupModal()" class="text-gray-400 text-2xl leading-none">&times;</button>
                </div>
                <div class="p-5 max-h-[60vh] overflow-y-auto" id="md-isi"></div>
                <div class="p-4 bg-gray-50 border-t flex gap-2">
                    <a id="btn-pdf-modal" href="#" target="_blank"
                        class="flex-1 bg-emerald-600 py-3 rounded-xl text-center text-xs font-bold text-white uppercase shadow-lg">PDF</a>
                    <button onclick="cetakStruk()"
                        class="flex-1 bg-indigo-600 py-3 rounded-xl text-xs font-bold text-white uppercase shadow-lg">Cetak</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        var dataDetailGlobal = null;

        function bukaModal(id) {
            const modal = document.getElementById('modalLaporan');
            const isi = document.getElementById('md-isi');
            const btnPdf = document.getElementById('btn-pdf-modal');

            document.getElementById('md-id').innerText = id;
            modal.classList.remove('hidden');
            isi.innerHTML = '<p class="text-center py-4 text-xs animate-pulse">MEMUAT...</p>';

            btnPdf.href = `/laporan/penjualan/${id}/pdf`;

            fetch(`/laporan/penjualan/${id}`)
                .then(res => res.json())
                .then(data => {
                    dataDetailGlobal = data;
                    const items = data.details || [];

                    // Gabungkan header petugas dan wrapper item dalam satu variabel html
                    let html = `
                    <div class="mb-4 p-2 bg-indigo-50 rounded-lg border border-indigo-100">
                        <p class="text-[9px] text-indigo-400 font-bold uppercase">Petugas Input</p>
                        <p class="text-xs font-bold text-indigo-700">${data.petugas || 'System'}</p>
                    </div>
                    <div class="text-xs space-y-2">`;

                    if (items.length === 0) {
                        html += `<p class="text-center text-gray-400 py-4">Tidak ada detail menu ditemukan</p>`;
                    } else {
                        items.forEach(item => {
                            const namaMenu = (item.menu && item.menu.nama_menu) ? item.menu.nama_menu : 'Menu';
                            const qty = item.jumlah_terjual || 0;
                            const subtotal = parseInt(item.subtotal) || 0;

                            html += `
                            <div class="flex justify-between border-b border-gray-50 pb-2">
                                <span>
                                    <b class="uppercase text-gray-700">${namaMenu}</b><br>
                                    <span class="text-gray-400">x${qty}</span>
                                </span>
                                <span class="font-bold text-gray-800">Rp${subtotal.toLocaleString('id-ID')}</span>
                            </div>`;
                        });
                    }

                    const total = parseInt(data.total_penjualan) || 0;
                    html += `
                        <div class="pt-4 flex justify-between items-center font-black text-indigo-600 text-sm">
                            <span>TOTAL BAYAR</span>
                            <span>Rp${total.toLocaleString('id-ID')}</span>
                        </div>
                    </div>`;

                    isi.innerHTML = html;
                })
                .catch(err => {
                    isi.innerHTML = '<p class="text-center text-red-500 text-xs">GAGAL MEMUAT DATA</p>';
                });
        }

        function tutupModal() {
            document.getElementById('modalLaporan').classList.add('hidden');
        }

        function cetakStruk() {
            if (!dataDetailGlobal) return;
            const data = dataDetailGlobal;
            const items = data.details || [];
            const printWindow = window.open('', '_blank');

            let itemHtml = '';
            items.forEach(item => {
                const nama = item.menu ? item.menu.nama_menu : 'Menu';
                const qty = item.jumlah_terjual || 0;
                const sub = parseInt(item.subtotal || 0).toLocaleString('id-ID');
                itemHtml += `<div style="display:flex;justify-content:space-between;margin-bottom:4px;">
                    <span>${nama} (x${qty})</span><span>${sub}</span>
                </div>`;
            });

            printWindow.document.write(`
                <html><body style="font-family:monospace;width:280px;padding:10px;font-size:12px;">
                    <div style="text-align:center;margin-bottom:10px;">
                        <h3 style="margin:0;">INDOCHICKEN</h3>
                        <p style="margin:0;font-size:10px;">Petugas: ${data.petugas || 'System'}</p>
                        <p style="margin:0;font-size:10px;">ID: #${data.id}</p>
                    </div>
                    <hr style="border:0;border-top:1px dashed #000;">
                    ${itemHtml}
                    <hr style="border:0;border-top:1px dashed #000;">
                    <div style="display:flex;justify-content:space-between;font-weight:bold;">
                        <span>TOTAL</span><span>Rp${parseInt(data.total_penjualan).toLocaleString('id-ID')}</span>
                    </div>
                    <script>window.onload=function(){window.print();setTimeout(window.close,200);}<\/script>
                </body></html>
            `);
            printWindow.document.close();
        }
    </script>
</x-app-layout>
