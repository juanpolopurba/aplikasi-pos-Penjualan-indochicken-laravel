<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-lg md:text-xl text-gray-800 leading-tight">
            {{ __('Tambah Kategori Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- Tombol Kembali --}}
            <a href="{{ route('kategori.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-indigo-600 mb-4 transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
            </a>

            <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-gray-100">
                <div class="p-6 md:p-8">
                    <form method="POST" action="{{ route('kategori.store') }}">
                        @csrf

                        <div>
                            <label for="nama_kategori" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">
                                Nama Kategori Pengeluaran
                            </label>
                            <input type="text" 
                                   name="nama_kategori" 
                                   id="nama_kategori" 
                                   class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm p-3"
                                   placeholder="Contoh: Bahan Baku, Listrik, Gaji..." 
                                   value="{{ old('nama_kategori') }}" 
                                   required 
                                   autofocus>
                            @error('nama_kategori')
                                <p class="mt-2 text-xs text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-8 flex flex-col gap-3">
                            <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 shadow-lg shadow-indigo-100 transition ease-in-out duration-150">
                                <i class="fas fa-save mr-2"></i> Simpan Kategori
                            </button>
                            
                            <p class="text-[10px] text-center text-gray-400 italic">
                                Pastikan nama kategori belum pernah terdaftar sebelumnya.
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Rekomendasi Kategori --}}
            <div class="mt-6 grid grid-cols-2 gap-3">
                <div class="p-3 bg-gray-50 rounded-lg border border-dashed border-gray-200 text-center">
                    <p class="text-[10px] text-gray-400 uppercase font-bold">Operasional</p>
                    <p class="text-xs text-gray-600">Sewa, Listrik, Air</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg border border-dashed border-gray-200 text-center">
                    <p class="text-[10px] text-gray-400 uppercase font-bold">Produksi</p>
                    <p class="text-xs text-gray-600">Ayam, Minyak, Tepung</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>