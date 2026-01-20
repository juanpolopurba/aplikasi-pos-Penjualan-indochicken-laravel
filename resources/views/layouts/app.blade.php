<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    {{-- 1. Tambahkan x-data="{ open: false }" pada body agar Alpine.js bekerja --}}
    <body x-data="{ open: false }" class="font-sans antialiased bg-gray-100">
        
        <div class="min-h-screen flex overflow-hidden">
            
            {{-- 2. SIDEBAR: Gunakan transisi untuk mobile --}}
            {{-- Kita pindahkan logika class ke dalam file sidebar.blade.php atau bungkus di sini --}}
            <div :class="open ? 'translate-x-0' : '-translate-x-full'" 
                 class="fixed inset-y-0 left-0 z-50 w-64 bg-white transition-transform duration-300 transform md:relative md:translate-x-0">
                @include('layouts.sidebar')
            </div>

            {{-- 3. OVERLAY: Klik area gelap untuk menutup sidebar di mobile --}}
            <div x-show="open" @click="open = false" class="fixed inset-0 z-40 bg-black opacity-50 md:hidden"></div>

            {{-- 4. WRAPPER UTAMA --}}
            {{-- Tambahkan flex-1 dan w-0 agar konten tidak melebar berantakan --}}
            <div class="flex-1 flex flex-col min-h-screen w-0 overflow-y-auto">
                
                {{-- NAVBAR ATAS --}}
                @include('layouts.navigation')

                {{-- HEADER HALAMAN --}}
                @isset($header)
                    <header class="bg-white shadow no-print">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                {{-- ISI KONTEN UTAMA --}}
                <main class="flex-grow py-6 md:py-12">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>

                {{-- FOOTER --}}
                <footer class="bg-white border-t border-gray-200 py-4 text-center text-sm text-gray-500 no-print">
                    &copy; {{ date('Y') }} POS Application. All rights reserved.
                </footer>
            </div>
        </div>

        {{-- SCRIPT POS (Tetap sama seperti kode Anda) --}}
        <script>
            console.log("SKRIP POS DIJALANKAN.");
            document.addEventListener('DOMContentLoaded', function () {
                // ... (seluruh script POS Anda tetap di sini)
            });
        </script>

        @stack('scripts') 
    </body>
</html>