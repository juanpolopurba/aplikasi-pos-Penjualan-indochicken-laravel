<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Menu: ') }} {{ $menu->nama_menu }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('menu.update', $menu->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="nama_menu" :value="__('Nama Menu')" />
                            <x-text-input id="nama_menu" class="block mt-1 w-full" type="text" name="nama_menu" :value="old('nama_menu', $menu->nama_menu)" required autofocus />
                            <x-input-error :messages="$errors->get('nama_menu')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <x-input-label for="kategori" :value="__('Kategori')" />
                                <select id="kategori" name="kategori" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="Makanan" {{ old('kategori', $menu->kategori) == 'Makanan' ? 'selected' : '' }}>Makanan</option>
                                    <option value="Minuman" {{ old('kategori', $menu->kategori) == 'Minuman' ? 'selected' : '' }}>Minuman</option>
                                    <option value="Snack" {{ old('kategori', $menu->kategori) == 'Snack' ? 'selected' : '' }}>Snack</option>
                                </select>
                                <x-input-error :messages="$errors->get('kategori')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="harga" :value="__('Harga (Rp)')" />
                                <x-text-input id="harga" class="block mt-1 w-full" type="number" name="harga" :value="old('harga', $menu->harga)" required />
                                <x-input-error :messages="$errors->get('harga')" class="mt-2" />
                            </div>
                        </div>

                        <div class="mb-6">
                            <x-input-label for="status" :value="__('Status Stok')" />
                            <select id="status" name="status" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="tersedia" {{ old('status', $menu->status) == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                <option value="habis" {{ old('status', $menu->status) == 'habis' ? 'selected' : '' }}>Habis</option>
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('menu.index') }}" class="text-sm text-gray-600 underline hover:text-gray-900 mr-4">
                                {{ __('Batal') }}
                            </a>
                            <x-primary-button>
                                {{ __('Update Menu') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>