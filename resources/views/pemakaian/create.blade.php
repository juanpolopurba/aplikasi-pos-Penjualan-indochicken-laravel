<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Input Pemakaian Bahan') }}
        </h2>
    </x-slot>


    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('pemakaian.store') }}" method="POST">
                    @csrf

                    {{-- Jika Admin/Manager, tampilkan pilihan --}}
                    @if (in_array(auth()->user()->role, ['admin', 'manager']))
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700">Pilih Cabang</label>
                            <select name="cabang_id" id="cabang_id" required
                                class="w-full mt-1 border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Pilih Lokasi --</option>
                                @foreach ($cabangs as $cabang)
                                    <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        {{-- Jika Kasir, langsung ambil cabang_id si user secara tersembunyi --}}
                        <input type="hidden" name="cabang_id" id="cabang_id" value="{{ auth()->user()->cabang_id }}">

                        {{-- Tambahkan info cabang agar kasir tidak bingung --}}
                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700">Lokasi Cabang</label>
                            <p class="text-sm text-gray-600">
                                {{ auth()->user()->cabang->nama_cabang ?? 'Cabang Tidak Terdeteksi' }}</p>
                        </div>
                    @endif

                    {{-- Dropdown Bahan Baku --}}
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700">Pilih Bahan Baku</label>
                        <select name="bahan_baku_id" class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                            <option value="">-- Pilih Cabang Terlebih Dahulu --</option>
                        </select>
                    </div>

                    {{-- Sisanya tetap sama... --}}
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700">Jumlah Dipakai</label>
                        <input type="number" step="0.01" name="jumlah"
                            class="w-full mt-1 border-gray-300 rounded-md shadow-sm" required>
                    </div>

                    {{-- Input Keterangan --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700">Keterangan (Opsional)</label>
                        <textarea name="keterangan" rows="3"
                            class="w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Contoh: Digunakan untuk produksi ayam goreng pagi hari"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-orange-500 text-white font-bold py-3 rounded-lg">
                        Simpan Pemakaian
                    </button>
                </form>

            </div>
        </div>
    </div>

   <script>
    const cabangInput = document.getElementById('cabang_id');
    const bahanSelect = document.getElementsByName('bahan_baku_id')[0];

    function loadBahan(cabangId) {
        if (!cabangId) return;

        bahanSelect.innerHTML = '<option value="">-- Loading... --</option>';

        fetch(`/get-stok-bahan/${cabangId}`)
            .then(response => response.json())
            .then(data => {
                bahanSelect.innerHTML = '<option value="">-- Pilih Bahan --</option>';
                if (data.length === 0) {
                    bahanSelect.innerHTML = '<option value="">Stok kosong di cabang ini</option>';
                }
                data.forEach(item => {
                    const stokTerformat = parseFloat(item.stok_saat_ini).toLocaleString('id-ID');
                    bahanSelect.innerHTML += `
                        <option value="${item.id}">
                            ${item.nama} (Sisa: ${stokTerformat} ${item.satuan})
                        </option>`;
                });
            });
    }

    // Jika dropdown berubah (untuk Admin/Manager)
    cabangInput.addEventListener('change', function() {
        loadBahan(this.value);
    });

    // AUTO-LOAD jika user adalah Kasir (cabang_id sudah ada di hidden input)
    window.addEventListener('DOMContentLoaded', (event) => {
        if (cabangInput.value) {
            loadBahan(cabangInput.value);
        }
    });
</script>
</x-app-layout>
