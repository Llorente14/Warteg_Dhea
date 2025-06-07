<!-- resources/views/livewire/view-order.blade.php -->
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6">Review Order</h2>

            <!-- Order Items -->
            <div class="space-y-4 mb-6">
                @foreach($cart as $menuId => $item)
                    <div class="flex items-start justify-between p-4 bg-gray-50 rounded-lg relative">
                        <!-- Delete Button (X) -->
                        <button 
                            wire:click="removeItem({{ $menuId }})" 
                            class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>

                        <!-- Item Details -->
                        <div class="flex-1">
                            <h3 class="font-semibold">{{ $item['name'] }}</h3>
                            <p class="text-gray-600">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>

                        <!-- Quantity Controls -->
                        <div class="flex items-center gap-2">
                            <button 
                                wire:click="decrementQuantity({{ $menuId }})"
                                wire:loading.attr="disabled"
                                wire:target="decrementQuantity({{ $menuId }})"
                                class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded-full transition-colors"
                            >
                                <span class="text-xl">−</span>
                            </button>
                            <span class="w-8 text-center font-medium">{{ $item['quantity'] }}</span>
                            <button 
                                wire:click="incrementQuantity({{ $menuId }})"
                                wire:loading.attr="disabled"
                                wire:target="incrementQuantity({{ $menuId }})"
                                class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded-full transition-colors"
                            >
                                <span class="text-xl">+</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- General Notes -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">General Notes</label>
                <textarea
                    wire:model="notes"
                    wire:change="updateGeneralNote"
                    placeholder="Add general notes for your order..."
                    class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    rows="3"
                ></textarea>
            </div>

            <!-- Total -->
            <div class="flex justify-between items-center text-lg font-semibold mb-6">
                <span>Total:</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>

            <!-- Actions -->
            <div class="flex justify-between">
                <a
                    href="/order"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                >
                    Add More Items
                </a>
                <a
                    href="/payment"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700"
                >
                    Continue to Payment
                </a>
            </div>
        </div>
    </div>
</div>
