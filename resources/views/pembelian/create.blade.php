<x-app-layout>
    <div class="py-12" x-data="{ items: [{ bahan_id: '', kuantitas: 1, harga_satuan: 0 }] }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-6">Input Pembelian Bahan Baku</h2>

                {{-- Alert Error --}}
                @if ($errors->any() || session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <strong>Terjadi Kesalahan!</strong>
                        <ul class="mt-2">
                            @if (session('error'))
                                <li>{{ session('error') }}</li>
                            @endif
                            @foreach ($errors->all() as $error)
                                <li>- {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Alert Success --}}
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('pembelian.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        {{-- Input Tanggal --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Transaksi</label>
                            <input type="date" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- Input Supplier --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Supplier / Toko</label>
                            <input type="text" name="supplier" value="{{ old('supplier') }}"
                                placeholder="Contoh: Toko Sembako Jaya"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Cabang Tujuan</label>
                            <select name="cabang_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">

                                @if (auth()->user()->role === 'admin' || auth()->user()->role === 'manager')
                                    <option value="">-- Pilih Cabang --</option>
                                    @foreach ($cabangs as $c)
                                        <option value="{{ $c->id }}"
                                            {{ old('cabang_id') == $c->id ? 'selected' : '' }}>
                                            {{ $c->nama_cabang }} {{-- Pastikan kolom ini sesuai dengan di tabel cabang Anda --}}
                                        </option>
                                    @endforeach
                                @else
                                    {{-- Jika user bukan admin, otomatis pilih cabang tempat dia bertugas --}}
                                    <option value="{{ auth()->user()->cabang_id }}" selected>
                                        {{ auth()->user()->cabang->nama_cabang ?? 'Cabang Terpilih' }}
                                    </option>
                                @endif

                            </select>
                            @error('cabang_id')
                                <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <table class="w-full mb-4">
                        <thead>
                            <tr class="text-left bg-gray-50 border-b">
                                <th class="p-2 w-1/2">Bahan Baku</th>
                                <th class="p-2">Jumlah</th>
                                <th class="p-2">Harga Satuan</th>
                                <th class="p-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr class="border-b">
                                    <td class="p-1">
                                        <select :name="`items[${index}][bahan_id]`" x-model="item.bahan_id"
                                            class="w-full rounded-md border-gray-300 text-sm">
                                            <option value="">-- Pilih Bahan --</option>
                                            @foreach ($bahanBaku as $b)
                                                <option value="{{ $b->id }}">
                                                    {{ $b->nama }} ({{ $b->satuan }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-1">
                                        <input type="number" step="0.01" :name="`items[${index}][kuantitas]`"
                                            x-model="item.kuantitas" class="w-full rounded-md border-gray-300 text-sm">
                                    </td>
                                    <td class="p-1">
                                        <input type="number" :name="`items[${index}][harga_satuan]`"
                                            x-model="item.harga_satuan"
                                            class="w-full rounded-md border-gray-300 text-sm">
                                    </td>
                                    <td class="p-1 text-center">
                                        <button type="button" @click="items.splice(index, 1)" x-show="items.length > 1"
                                            class="text-red-600 font-bold px-2 hover:bg-red-100 rounded">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <div class="flex justify-between items-center mt-6">
                        <button type="button" @click="items.push({ bahan_id: '', kuantitas: 1, harga_satuan: 0 })"
                            class="bg-gray-800 text-white px-4 py-2 rounded text-sm font-bold hover:bg-gray-700">
                            + Tambah Item
                        </button>

                        <div class="flex items-center space-x-4">
                            <a href="{{ route('pembelian.index') }}"
                                class="text-gray-600 hover:underline text-sm">Batal</a>
                            <button type="submit"
                                class="bg-indigo-600 text-white px-6 py-2 rounded font-bold shadow-md hover:bg-indigo-700">
                                Simpan Pembelian
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
