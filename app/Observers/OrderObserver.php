<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Stock;
use App\Models\StockUsage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Opsional: untuk logging

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
        // dan sebelumnya bukan 'takeaway' atau baru pertama kali diubah ke takeaway.
        // Atau jika order baru saja berubah status menjadi 'completed' dari 'pending'/'paid' dan tipenya 'takeaway'
        if ($order->isDirty('type') && $order->type === 'takeaway') {
            $this->handleTakeawayStock($order);
        }
        // Anda juga bisa menambahkan logic untuk memproses stok saat status berubah menjadi 'completed'
        // if ($order->isDirty('status') && $order->status === 'completed' && $order->type === 'takeaway') {
        //     $this->handleTakeawayStock($order);
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

        // Pastikan stok belum pernah diproses untuk order ini (misal, tidak ada StockUsage untuk order ini)
        // Ini untuk menghindari pengurangan stok berulang jika order diupdate berkali-kali
        if (StockUsage::where('order_id', $order->id)->exists()) {
            Log::info("Stok takeaway untuk Order ID {$order->id} sudah pernah diproses. Melewatkan.");
            return;
        }

        DB::transaction(function () use ($order) {
            // Temukan stok kemasan takeaway (misal: "Kotak Makanan", "Kantong Plastik", dll.)
            // Anda mungkin perlu membuat mapping antara menu dan kemasan takeaway
            // Untuk contoh ini, kita asumsikan ada satu jenis "kemasan takeaway" umum yang dikurangi per order.
            // Jika setiap item menu memiliki kemasan berbeda, Anda perlu logic yang lebih kompleks
            // yang melibatkan OrderItem dan Stok yang sesuai.

            // Untuk contoh sederhana: Kurangi 1 unit kemasan takeaway per order takeaway
            $takeawayPackaging = Stock::where('type', 'kemasan takeaway')->first();

            if (!$takeawayPackaging) {
                Log::warning("Tidak ada stok dengan tipe 'kemasan takeaway' ditemukan. Order ID: {$order->id}");
                // throw new \Exception('Takeaway packaging stock not found.'); // Atau tangani error
                return;
            }

            // Jumlah kemasan yang digunakan (bisa 1 per order, atau berdasarkan item dalam order)
            // Jika ingin 1 per order:
            $quantityToReduce = 1;
            // Jika ingin berdasarkan jumlah item menu *yang membutuhkan kemasan*:
            // $quantityToReduce = $order->items->sum('quantity'); // Ini bisa sangat bervariasi

            if ($takeawayPackaging->quantity >= $quantityToReduce) {
                $takeawayPackaging->quantity -= $quantityToReduce;
                $takeawayPackaging->save();

                // Insert ke stock_usages
                StockUsage::create([
                    'order_id' => $order->id,
                    'stock_id' => $takeawayPackaging->id,
                    'used_quantity' => $quantityToReduce,
                ]);

                Log::info("Stok takeaway '{$takeawayPackaging->name}' dikurangi sebanyak {$quantityToReduce} untuk Order ID: {$order->id}");
            } else {
                Log::warning("Stok takeaway '{$takeawayPackaging->name}' tidak cukup untuk Order ID: {$order->id}. Sisa stok: {$takeawayPackaging->quantity}");
                // Opsional: Lemparkan exception atau kirim notifikasi ke admin
                // throw new \Exception('Not enough takeaway packaging stock.');
            }

            // Cek jika quantity <= 0, maka update is_available = false
            if ($takeawayPackaging->quantity <= 0) {
                $takeawayPackaging->is_available = false;
                $takeawayPackaging->save();
                Log::info("Stok '{$takeawayPackaging->name}' sekarang tidak tersedia (quantity <= 0).");
            }
        });
    }

  
}