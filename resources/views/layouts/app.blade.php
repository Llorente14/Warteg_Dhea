<!DOCTYPE html>
<html  class=" bg-gray-100" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    {{-- Styles --}}
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Self Order App') }}</title>

    {{-- Memuat aset CSS dan JS menggunakan Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles {{-- Ini penting untuk gaya CSS Livewire --}}
    @stack('styles') {{-- Jika Anda memiliki gaya tambahan yang didorong dari komponen --}}
</head>
<body class="h-full">
        <main>
                {{ $slot }}
        </main>
    @livewireScripts {{-- Ini penting untuk fungsionalitas JavaScript Livewire --}}
    @stack('scripts') {{-- Jika Anda memiliki skrip tambahan yang didorong dari komponen --}}
</body>
</html>