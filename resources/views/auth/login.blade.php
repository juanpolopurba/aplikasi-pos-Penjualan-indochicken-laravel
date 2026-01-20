<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-3xl font-black text-red-600 tracking-tighter">
            INDO<span class="text-orange-500">CHICKEN</span>
        </h1>
        <p class="text-[10px] text-gray-500 uppercase tracking-widest mt-1 italic font-bold">Sistem Manajemen POS</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="username" :value="__('Username')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="w-full justify-center py-3 bg-red-600 hover:bg-red-700">
                {{ __('Masuk ke Sistem') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>