<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Stock;
use App\Models\StockUsage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Panggil logic pengurangan stok saat order baru dibuat
        $this->handleTakeawayStock($order);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Hanya jalankan jika 'type' berubah menjadi 'takeaway'
        // dan sebelumnya bukan 'takeaway'.
        // Atau jika order baru saja berubah status menjadi 'completed' dari 'pending'/'paid' dan tipenya 'takeaway'
        if ($order->isDirty('type') && $order->type === 'takeaway') {
            $this->handleTakeawayStock($order);
        }
        // Anda juga bisa menambahkan logic untuk memproses stok saat status berubah menjadi 'completed'
        // if ($order->isDirty('status') && $order->status === 'completed' && $order->type === 'takeaway') {
        //      $this->handleTakeawayStock($order);
        // }
    }

    /**
     * Logic untuk mengurangi stok kemasan takeaway.
     */
    protected function handleTakeawayStock(Order $order): void
    {
        // Pastikan order ini memang tipe 'takeaway'
        if ($order->type !== 'takeaway') {
            return;
        }

        // Pastikan stok belum pernah diproses untuk order ini
        if (StockUsage::where('order_id', $order->id)->exists()) {
            Log::info("Stok takeaway untuk Order ID {$order->id} sudah pernah diproses. Melewatkan.");
            return;
        }

        DB::transaction(function () use ($order) {
            // Ambil semua stok yang memiliki type 'kemasan takeaway' dan masih tersedia
            // Ini akan mengembalikan Collection dari model Stock
            $takeawayPackages = Stock::where('type', 'kemasan takeaway')
                                     ->where('is_available', true) // Hanya kurangi stok yang aktif/tersedia
                                     ->get();

            // Cek apakah ada stok kemasan takeaway yang ditemukan.
            // Gunakan `isEmpty()` untuk Collection.
            if ($takeawayPackages->isEmpty()) {
                Log::warning("Tidak ada stok dengan tipe 'kemasan takeaway' yang tersedia. Order ID: {$order->id}");
                return; // Keluar jika tidak ada stok yang ditemukan
            }

            // Jumlah kemasan yang digunakan per jenis stok takeaway.
            // Asumsi: setiap jenis kemasan takeaway dikurangi 1 unit per order takeaway.
            $quantityToReducePerItem = 1; 
            
            // Loop melalui setiap item stok kemasan takeaway yang ditemukan
            foreach ($takeawayPackages as $stockItem) {
                // Pastikan stok cukup sebelum mengurangi
                if ($stockItem->quantity >= $quantityToReducePerItem) {
                    $stockItem->quantity -= $quantityToReducePerItem;
                    $stockItem->save(); // Simpan perubahan kuantitas

                    // Insert ke stock_usages untuk mencatat penggunaan
                    StockUsage::create([
                        'order_id' => $order->id,
                        'stock_id' => $stockItem->id, // Gunakan ID dari stockItem saat ini dalam loop
                        'used_quantity' => $quantityToReducePerItem,
                    ]);

                    Log::info("Stok takeaway '{$stockItem->name}' (ID: {$stockItem->id}) dikurangi sebanyak {$quantityToReducePerItem} untuk Order ID: {$order->id}");

                    // Cek jika quantity <= 0, maka update is_available = false
                    if ($stockItem->quantity <= 0) {
                        $stockItem->is_available = false;
                        $stockItem->save(); // Simpan perubahan is_available
                        Log::info("Stok '{$stockItem->name}' (ID: {$stockItem->id}) sekarang tidak tersedia (quantity <= 0).");
                    }
                } else {
                    // Log peringatan jika stok tidak cukup untuk jenis kemasan tertentu
                    Log::warning("Stok takeaway '{$stockItem->name}' (ID: {$stockItem->id}) tidak cukup untuk Order ID: {$order->id}. Sisa stok: {$stockItem->quantity}");
                    // Opsional: Anda bisa memilih untuk melempar exception di sini
                    // jika ketiadaan stok dari satu jenis kemasan dianggap fatal untuk order ini.
                    // throw new \Exception("Not enough stock for {$stockItem->name}");
                }
            }
        });
    }
}