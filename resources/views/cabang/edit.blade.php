<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Cabang: ') }} {{ $cabang->nama_cabang }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('cabang.update', $cabang->id) }}">
                        @csrf @method('PUT')
                        
                        <div class="mb-4">
                            <x-input-label for="nama_cabang" :value="__('Nama Cabang')" />
                            <x-text-input id="nama_cabang" class="block mt-1 w-full" type="text" name="nama_cabang" :value="old('nama_cabang', $cabang->nama_cabang)" required />
                            <x-input-error :messages="$errors->get('nama_cabang')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="telepon" :value="__('Nomor Telepon')" />
                            <x-text-input id="telepon" class="block mt-1 w-full" type="text" name="telepon" :value="old('telepon', $cabang->telepon)" />
                            <x-input-error :messages="$errors->get('telepon')" class="mt-2" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="alamat" :value="__('Alamat Lengkap')" />
                            <textarea id="alamat" name="alamat" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('alamat', $cabang->alamat) }}</textarea>
                            <x-input-error :messages="$errors->get('alamat')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('cabang.index') }}" class="text-sm text-gray-600 underline hover:text-gray-900 mr-4">{{ __('Batal') }}</a>
                            <x-primary-button>{{ __('Update Cabang') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>