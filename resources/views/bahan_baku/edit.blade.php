<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Bahan Baku: ') }} {{ $bahanBaku->nama }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('bahan_baku.update', $bahanBaku->id) }}">
                        @csrf
                        @method('PUT')

                        {{-- Nama Bahan: Sesuaikan name="nama" agar sama dengan database --}}
                        <div class="mb-4">
                            <x-input-label for="nama" :value="__('Nama Bahan Baku')" />
                            <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama"
                                :value="old('nama', $bahanBaku->nama)" required />
                            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            {{-- Stok: Mengambil stok_saat_ini dari inventory --}}
                            <div>
                                <x-input-label for="stok" :value="__('Update Stok')" />
                                <x-text-input id="stok" class="block mt-1 w-full" type="number" step="0.01"
                                    name="stok" :value="old('stok', $bahanBaku->stok_saat_ini)" required />
                                <x-input-error :messages="$errors->get('stok')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="satuan" :value="__('Satuan')" />
                                <select id="satuan" name="satuan"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    @php $units = ['Gram', 'Kilogram', 'Liter', 'Mili Liter', 'Pcs']; @endphp
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit }}"
                                            {{ old('satuan', $bahanBaku->satuan) == $unit ? 'selected' : '' }}>
                                            {{ $unit }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('satuan')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="cabang_id" :value="__('Lokasi Cabang')" />
                            <select id="cabang_id" name="cabang_id"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach ($cabangs as $cabang)
                                    <option value="{{ $cabang->id }}"
                                        {{ old('cabang_id', $bahanBaku->cabang_id) == $cabang->id ? 'selected' : '' }}>
                                        {{ $cabang->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('cabang_id')" class="mt-2" />
                        </div>

                        {{-- Input Harga Terakhir --}}
                        <div class="mb-4">
                            <x-input-label for="harga_terakhir" :value="__('Harga Terakhir (Rp)')" />
                            {{-- <x-text-input id="harga_terakhir" class="block mt-1 w-full" type="number"
                                name="harga_terakhir" :value="old('harga_terakhir', $bahanBaku->harga_terakhir)" required />
                            <x-input-error :messages="$errors->get('harga_terakhir')" class="mt-2" /> --}}
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('bahan_baku.index') }}"
                                class="text-sm text-gray-600 underline hover:text-gray-900 mr-4">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Data') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
