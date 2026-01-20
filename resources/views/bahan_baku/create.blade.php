<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Stok Bahan Baku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- TAMBAHKAN BLOK INI: Alert Pesan Sukses --}}
                    @if (session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- TAMBAHKAN BLOK INI: Alert Pesan Error Validasi --}}
                    @if ($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                            <strong>Ups! Ada masalah:</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('bahan_baku.store') }}">
                        @csrf
                        {{-- Isi form Anda tetap sama seperti sebelumnya... --}}
                        <div class="mb-4">
                            <x-input-label for="nama" :value="__('Nama Bahan Baku')" />
                            <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama"
                                :value="old('nama')" placeholder="Contoh: Biji Kopi Arabica" required autofocus />
                            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                        </div>

                        {{-- Dan seterusnya --}}

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="stok_saat_ini" :value="__('Stok_saat_ini')" />
                                <x-text-input id="stok" class="block mt-1 w-full" type="number" step="0.01"
                                    name="stok_saat_ini" :value="old('stok_saat_ini')" required />
                                <x-input-error :messages="$errors->get('stok')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="satuan" :value="__('Satuan')" />
                                <select id="satuan" name="satuan"
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="Gram">Gram (g)</option>
                                    <option value="Kilogram">Kilogram (kg)</option>
                                    <option value="Liter">Liter (L)</option>
                                    <option value="Mili Liter">Mili Liter (ml)</option>
                                    <option value="Pcs">Pcs / Biji</option>
                                </select>
                                <x-input-error :messages="$errors->get('satuan')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-bold text-gray-700 uppercase mb-2">Lokasi Cabang</label>

                            @if (Auth::user()->role === 'admin' || Auth::user()->role === 'manager')
                                {{-- Admin bebas pilih cabang --}}
                                <select name="cabang_id"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500">
                                    @foreach ($cabangs as $cabang)
                                        <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            @else
                                {{-- Kasir hanya melihat teks nama cabangnya, ID dikirim via hidden input --}}
                                <div class="p-3 bg-gray-100 border border-gray-200 rounded-lg flex items-center gap-3">
                                    <span class="w-2 h-2 bg-indigo-500 rounded-full"></span>
                                    <span
                                        class="font-bold text-gray-800">{{ Auth::user()->cabang->nama_cabang ?? 'Cabang Terdaftar' }}</span>
                                </div>
                                <input type="hidden" name="cabang_id" value="{{ Auth::user()->cabang_id }}">
                            @endif
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('bahan_baku.index') }}"
                                class="text-sm text-gray-600 underline hover:text-gray-900 mr-4">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Simpan Bahan') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
