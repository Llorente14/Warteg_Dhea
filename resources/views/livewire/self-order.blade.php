<div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">Warteg Bu Dhea</h1>
                <button class="relative" @click="$dispatch('open-cart')">
                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    @if(count($cart) > 0)
                        <span class="absolute -top-2 -right-2 bg-pink-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm">
                            {{ count($cart) }}
                        </span>
                    @endif
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Categories and Menu Items -->
        <div class="px-4 py-6 sm:px-0">
            @foreach($categories as $category)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4">{{ $category->name }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($category->menu as $menu)
                            <div class="bg-white rounded-lg shadow p-6">
                                <h3 class="text-lg font-semibold">{{ $menu->name }}</h3>
                                <p class="text-gray-600 mt-2">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                <button 
                                    wire:click="addToCart({{ $menu->id }})"
                                    class="mt-4 w-full bg-pink-500 text-white py-2 px-4 rounded-md hover:bg-pink-600 transition"
                                >
                                    Tambah ke Keranjang
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Cart Sidebar -->
        <div x-data="{ open: false }" 
             x-show="open" 
             @open-cart.window="open = true"
             class="fixed inset-y-0 right-0 w-96 bg-white shadow-xl transform transition-transform duration-300"
             :class="{ 'translate-x-0': open, 'translate-x-full': !open }">
            <div class="h-full flex flex-col">
                <div class="p-4 border-b">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-semibold">Keranjang</h2>
                        <button @click="open = false" class="text-gray-500 hover:text-gray-700">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-4">
                    @foreach($cart as $item)
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="font-medium">{{ $item['name'] }}</h3>
                                <p class="text-gray-600">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    wire:click="removeFromCart({{ $item['id'] }})"
                                    class="text-gray-500 hover:text-gray-700"
                                >
                                    -
                                </button>
                                <span>{{ $item['quantity'] }}</span>
                                <button 
                                    wire:click="addToCart({{ $item['id'] }})"
                                    class="text-gray-500 hover:text-gray-700"
                                >
                                    +
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="p-4 border-t">
                    <div class="flex justify-between items-center mb-4">
                        <span class="font-semibold">Total:</span>
                        <span class="font-semibold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Metode Pembayaran</label>
                        <select 
                            wire:model="paymentMethod"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-pink-500 focus:ring-pink-500"
                        >
                            <option value="cash">Tunai</option>
                            <option value="qris">QRIS</option>
                        </select>
                    </div>

                    <button 
                        wire:click="checkout"
                        class="w-full bg-pink-500 text-white py-3 px-4 rounded-md hover:bg-pink-600 transition"
                    >
                        Checkout
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if($showSuccessMessage)
            <div 
                x-data="{ show: true }"
                x-show="show"
                x-init="setTimeout(() => show = false, 3000)"
                class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg"
            >
                Pesanan berhasil! Silakan tunggu pesanan Anda diproses.
            </div>
        @endif
    </main>
</div>