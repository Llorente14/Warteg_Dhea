<?php

namespace Database\Seeders;



use Illuminate\Database\Seeder;
use App\Models\Stock;

class StockSeeder extends Seeder
{
    public function run(): void
    {
        $stocks = [
            // Dine-In
            ['name' => 'Piring Beling', 'type' => 'alat makan', 'quantity' => 25],
            ['name' => 'Piring Rotan', 'type' => 'alat makan', 'quantity' => 30],
            ['name' => 'Kertas Nasi', 'type' => 'alat makan', 'quantity' => 200],
            ['name' => 'Gelas Beling', 'type' => 'alat makan', 'quantity' => 30],
            ['name' => 'Sendok Makan', 'type' => 'alat makan', 'quantity' => 60],

            // Takeaway
            ['name' => 'Sendok Plastik', 'type' => 'kemasan takeaway', 'quantity' => 200],
            ['name' => 'Styrofoam', 'type' => 'kemasan takeaway', 'quantity' => 200],
            ['name' => 'Gelas Plastik', 'type' => 'kemasan takeaway', 'quantity' => 150],
            ['name' => 'Plastik Tahan Panas', 'type' => 'kemasan takeaway', 'quantity' => 300],

            // Alat Masak
            ['name' => 'Dandang', 'type' => 'alat masak', 'quantity' => 1],
            ['name' => 'Panci', 'type' => 'alat masak', 'quantity' => 1],
            ['name' => 'Spatula', 'type' => 'alat masak', 'quantity' => 2],
            ['name' => 'Saringan Gorengan', 'type' => 'alat masak', 'quantity' => 2],
            ['name' => 'Wajan', 'type' => 'alat masak', 'quantity' => 1],
            ['name' => 'Peti Es', 'type' => 'alat masak', 'quantity' => 1],
            ['name' => 'Kompor', 'type' => 'alat masak', 'quantity' => 2],
            ['name' => 'Baskom', 'type' => 'alat masak', 'quantity' => 1],
            ['name' => 'Pisau', 'type' => 'alat masak', 'quantity' => 2],
        ];

        foreach ($stocks as $stock) {
            Stock::create([
                'name' => $stock['name'],
                'type' => $stock['type'],
                'quantity' => $stock['quantity'],
                'is_available' => $stock['quantity'] > 0,
            ]);
        }
    }
}

