<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Catat Pengeluaran Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('pengeluaran.store') }}">
                        @csrf

                        <div class="mb-4">
                            <x-input-label for="tanggal" :value="__('Tanggal Transaksi')" />
                            <x-text-input id="tanggal" class="block mt-1 w-full" type="date" name="tanggal"
                                :value="old('tanggal', date('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('tanggal')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="kategori_id" :value="__('Kategori Pengeluaran')" />
                            <select id="kategori_id" name="kategori_id"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($kategoris as $kat)
                                    <option value="{{ $kat->id }}"
                                        {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>
                                        {{ $kat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('kategori_id')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="deskripsi" :value="__('Deskripsi / Keterangan')" />
                            <x-text-input id="deskripsi" class="block mt-1 w-full" type="text" name="deskripsi"
                                :value="old('deskripsi')" placeholder="Contoh: Bayar Listrik Bulan Des" required />
                            <x-input-error :messages="$errors->get('deskripsi')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <x-input-label for="jumlah" :value="__('Jumlah (Rp)')" />
                                <x-text-input id="jumlah" class="block mt-1 w-full" type="number" name="jumlah"
                                    :value="old('jumlah')" required />
                                <x-input-error :messages="$errors->get('jumlah')" class="mt-2" />
                            </div>

                            <div class="mb-4">
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Cabang
                                    / Lokasi</label>

                                @if (in_array(Auth::user()->role, ['admin', 'manager']))
                                    {{-- Jika dia ADMIN, tampilkan dropdown agar bisa pilih semua cabang --}}
                                    <select name="cabang_id" id="cabang_id"
                                        class="w-full bg-white border border-gray-200 rounded-xl text-sm p-3 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="">-- Pilih Cabang --</option>
                                        @foreach ($cabangs as $cabang)
                                            <option value="{{ $cabang->id }}"
                                                {{ old('cabang_id') == $cabang->id ? 'selected' : '' }}>
                                                {{ $cabang->nama_cabang }}
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    {{-- Jika dia KASIR, sembunyikan dropdown, ganti dengan tampilan teks --}}
                                    <div class="flex items-center p-3 bg-gray-50 border border-gray-200 rounded-xl">
                                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse mr-3"></div>
                                        <span class="text-sm font-bold text-gray-700 uppercase">
                                            {{ Auth::user()->cabang->nama_cabang ?? 'Cabang Tidak Terdaftar' }}
                                        </span>
                                    </div>

                                    {{-- Input hidden supaya cabang_id tetap terkirim ke Controller --}}
                                    <input type="hidden" name="cabang_id" value="{{ Auth::user()->cabang_id }}">
                                @endif

                                @error('cabang_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('pengeluaran.index') }}"
                                class="text-sm text-gray-600 underline hover:text-gray-900 mr-4">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button class="bg-red-600 hover:bg-red-700">
                                {{ __('Simpan Pengeluaran') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
