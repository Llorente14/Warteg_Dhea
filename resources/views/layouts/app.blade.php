<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Self Order App') }}</title>

    {{-- Memuat aset CSS dan JS menggunakan Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles {{-- Ini penting untuk gaya CSS Livewire --}}
    @stack('styles') {{-- Jika Anda memiliki gaya tambahan yang didorong dari komponen --}}
</head>
<body class="bg-gray-900 text-gray-200 antialiased font-sans">
    {{-- Ini adalah tempat konten dari komponen Livewire SelfOrder Anda akan dirender --}}
    {{ $slot }}

    @livewireScripts {{-- Ini penting untuk fungsionalitas JavaScript Livewire --}}
    @stack('scripts') {{-- Jika Anda memiliki skrip tambahan yang didorong dari komponen --}}
</body>
</html>