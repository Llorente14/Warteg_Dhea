<!-- resources/views/livewire/self-order.blade.php -->
<div class="min-h-screen bg-gray-50">
    {{-- Notif Pemesanan Sukses/Tidak Sukses --}}
    @if (session()->has('message'))
    <div class="fixed top-4 right-4 z-50 max-w-sm w-full">
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 3000)"
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0"
             x-transition:enter-end="translate-y-0 opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg" 
             role="alert"
        >
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <!-- Success Icon -->
                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">
                        {{ session('message') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- Header -->
    <header class="bg-blue-600 shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-white">Warteg Bu Dhea</h1>
                
                <button 
                    wire:click="viewCart"
                    class="relative p-2 rounded-full transition {{ count($cart) > 0 ? 'bg-white hover:bg-gray-100 cursor-pointer' : 'bg-gray-200 cursor-not-allowed opacity-50' }}"
                    {{ count($cart) === 0 ? 'disabled' : '' }}
                >
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    @if(count($cart) > 0)
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm">
                            {{ count($cart) }}
                        </span>
                    @endif
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class=" md:px-10 lg:px-20">
        {{-- Responsive option type dine-in or takeaway --}}
        <div class="p-4"> 
            <h2 class="text-xl font-semibold mb-4 text-gray-800 dark:text-gray-200">Pilih Tipe Pesanan:</h2>
            <div class="flex flex-col md:flex-row gap-3 w-full max-w-md mx-auto bg-gray-100 dark:bg-gray-700 rounded-lg p-2 shadow-inner">
                <button
                    type="button"
                    wire:click="$set('orderType', 'dine-in')" {{-- Mengatur properti Livewire --}}
                    class="flex-1 px-4 py-2 text-center rounded-md font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2
                           {{-- Kelas dinamis berdasarkan properti orderType --}}
                           {{ $orderType === 'dine-in' ? 
                               'bg-blue-600 text-white shadow-md hover:bg-blue-700 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-400' : 
                               'bg-transparent text-gray-700 hover:bg-gray-200 focus:ring-gray-300 dark:text-gray-300 dark:hover:bg-gray-600 dark:focus:ring-gray-500' 
                           }}"
                >
                    Dine-in
                </button>
                <button
                    type="button"
                    wire:click="$set('orderType', 'takeaway')" {{-- Mengatur properti Livewire --}}
                    class="flex-1 px-4 py-2 text-center rounded-md font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2
                           {{-- Kelas dinamis berdasarkan properti orderType --}}
                           {{ $orderType === 'takeaway' ? 
                               'bg-blue-600 text-white shadow-md hover:bg-blue-700 focus:ring-blue-500 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-400' : 
                               'bg-transparent text-gray-700 hover:bg-gray-200 focus:ring-gray-300 dark:text-gray-300 dark:hover:bg-gray-600 dark:focus:ring-gray-500' 
                           }}"
                >
                    Takeaway
                </button>
            </div>

            {{-- Input tersembunyi yang terikat ke properti Livewire. Nilai ini akan otomatis terupdate. --}}
            <input type="hidden" name="type" wire:model="orderType"> 
        </div>

        <!-- Categories and Menu Items -->
        <div class="px-4 py-6 sm:px-0 ">
            @foreach($categories as $category)
                <div class="mb-4 mb:mb-8 ">
                    <h2 class="text-2xl font-bold mb-4 text-gray-800 md:mb-8 lg:mb-10">{{ $category->name }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 ">
                        @foreach($category->menu as $menu)
                            <div class="min-w-[140px] max-w-[300px] bg-white shadow-sm rounded-lg overflow-hidden p-1 mb-14 mx-2 lg:mb-10 lg:mx-0">
                                {{-- Mengubah src gambar agar bisa diakses dari storage --}}
                                
                                @if($menu->image)
                                    {{-- Gunakan helper asset() untuk mendapatkan URL publik gambar --}}
                                    <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-[130px] object-cover rounded-sm" />
                                @else
                                    {{-- Fallback jika gambar tidak ada atau kosong --}}
                                    <div class="w-full h-[130px] bg-gray-200 rounded-sm flex items-center justify-center text-gray-500 text-sm">
                                        Tidak Ada Gambar
                                    </div>
                                @endif

                                <div class="p-3 flex flex-col justify-between">
                                    <div>
                                        <a class="text-base font-bold text-gray-800 hover:underline">
                                            {{ $menu->name }}
                                        </a>
                                        <p class="text-sm text-gray-700 my-1">{{ $category->name }}</p>
                                        <p class="text-sm text-gray-600 mt-1 font-semibold">
                                            Rp {{ number_format($menu->price, 0, ',', '.') }}
                                        </p>
                                    </div>

                                    @if(!isset($selectedItem) || $selectedItem !== $menu->id)
                                        <button
                                            wire:click="selectItem({{ $menu->id }})"
                                            class="flex justify-center mt-3 w-full bg-pink-200 text-pink-600 font-medium text-sm py-1.5 rounded-lg border border-pink-300 hover:bg-pink-300 transition">
                                            Tambah
                                        </button>
                                    @else
                                        <div class="mt-3">
                                            <div class="flex items-center justify-between bg-gray-100 rounded-lg p-2">
                                                <button 
                                                    wire:click="decrementQty"
                                                    class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded-full"
                                                >
                                                    <span class="text-xl">−</span>
                                                </button>
                                                <span class="text-gray-800 font-medium">{{ $quantity }}</span>
                                                <button 
                                                    wire:click="incrementQty"
                                                    class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded-full"
                                                >
                                                    <span class="text-xl">+</span>
                                                </button>
                                            </div>
                                            <button
                                                wire:click="addToCartWithQty"
                                                class="flex justify-center mt-2 w-full bg-pink-200 text-pink-600 font-medium text-sm py-1.5 rounded-lg border border-pink-300 hover:bg-pink-300 transition"
                                            >
                                                Tambah ke Keranjang
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </main>

    <!-- Floating Cart Button (Mobile) -->
    @if(count($cart) > 0)
        <div class="fixed bottom-4 right-4 md:hidden">
            <button
                wire:click="viewCart"
                class="bg-blue-600 text-white px-6 py-3 rounded-full shadow-lg hover:bg-blue-700 transition flex items-center gap-2"
            >
                <span>View Cart ({{ count($cart) }})</span>
                <span class="font-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </button>
        </div>
    @endif
</div>

{{-- SVG --}}
{{-- <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg> --}}

                               