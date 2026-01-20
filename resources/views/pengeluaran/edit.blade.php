<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Catatan Pengeluaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('pengeluaran.update', $pengeluaran->id) }}">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="mb-4">
                            <x-input-label for="tanggal" :value="__('Tanggal')" />
                            <x-text-input id="tanggal" class="block mt-1 w-full" type="date" name="tanggal"
                                :value="old('tanggal', $pengeluaran->tanggal)" required />
                        </div>

                        <div class="mb-4">
                            <label for="cabang_id">Cabang</label>
                            <select id="cabang_id" name="cabang_id"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">-- Pilih Cabang --</option>
                                @foreach ($cabangs as $item)
                                    <option value="{{ $item->id }}"
                                        {{ old('cabang_id', $pengeluaran->cabang_id) == $item->id ? 'selected' : '' }}>
                                        {{ $item->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Menampilkan pesan error khusus cabang jika ada --}}
                            @error('cabang_id')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <x-input-label for="kategori_id" :value="__('Kategori')" />
                            <select id="kategori_id" name="kategori_id"
                                class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">
                                @foreach ($kategoris as $kat)
                                    <option value="{{ $kat->id }}"
                                        {{ $pengeluaran->kategori_id == $kat->id ? 'selected' : '' }}>
                                        {{ $kat->nama_kategori }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <x-input-label for="deskripsi" :value="__('Keterangan')" />
                            <x-text-input id="deskripsi" class="block mt-1 w-full" type="text" name="deskripsi"
                                :value="old('deskripsi', $pengeluaran->deskripsi)" required />
                        </div>

                        <div class="mb-6">
                            <x-input-label for="jumlah" :value="__('Jumlah (Rp)')" />
                            <x-text-input id="jumlah" class="block mt-1 w-full" type="number" name="jumlah"
                                :value="old('jumlah', $pengeluaran->jumlah)" required />
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            {{-- Tombol Batal --}}
                            <a href="{{ route('pengeluaran.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Batal') }}
                            </a>

                            {{-- Tombol Simpan --}}
                            <x-primary-button class="bg-indigo-600">
                                {{ __('Update Data') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
