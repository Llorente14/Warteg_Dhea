<!-- resources/views/livewire/payment.blade.php -->
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold mb-6 text-blue-500">Payment</h2>

            <!-- Customer Information -->
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input
                        type="text"
                        wire:model="name"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input
                        type="email"
                        wire:model="email"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input
                        type="tel"
                        wire:model="phone"
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                </div>
            </div>

            <!-- Payment Method -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="radio" wire:model="paymentMethod" value="cash" class="text-blue-600 focus:ring-blue-500">
                        <span class="ml-2">Cash</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" wire:model="paymentMethod" value="qris" class="text-blue-600 focus:ring-blue-500">
                        <span class="ml-2">QRIS</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" wire:model="paymentMethod" value="bank_transfer" class="text-blue-600 focus:ring-blue-500">
                        <span class="ml-2">Bank Transfer</span>
                    </label>
                </div>
            </div>

            <!-- QR Code (shown when QRIS is selected) -->
            @if($showQRCode)
                <div class="mb-6 text-center">
                    <div class="bg-gray-100 p-4 rounded-lg inline-block">
                        <!-- Replace with actual QR code -->
                        <div class="w-48 h-48 bg-gray-300 mx-auto mb-2"></div>
                        <p class="text-sm text-gray-600">Scan QR code to pay</p>
                    </div>
                </div>
            @endif

            <!-- Total -->
            <div class="flex justify-between items-center text-lg font-semibold mb-6">
                <span>Total:</span>
                <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>

            <!-- Submit Button -->
            <button
                wire:click="processPayment"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition"
            >
                Complete Order
            </button>
        </div>
    </div>
</div>
