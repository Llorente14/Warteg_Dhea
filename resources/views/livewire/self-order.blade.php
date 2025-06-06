<!-- resources/views/livewire/self-order.blade.php -->
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-blue-600 shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-white">Warteg Bu Dhea</h1>
                <button 
                    wire:click="viewCart"
                    class="relative bg-white p-2 rounded-full hover:bg-gray-100 transition"
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
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Categories and Menu Items -->
        <div class="px-4 py-6 sm:px-0">
            @foreach($categories as $category)
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-4 text-gray-800">{{ $category->name }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($category->menu as $menu)
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $menu->name }}</h3>
                                    <p class="text-blue-600 font-bold mt-2">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                                    <button 
                                        wire:click="addToCart({{ $menu->id }})"
                                        class="mt-4 w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition flex items-center justify-center gap-2"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Add to Cart
                                    </button>
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
