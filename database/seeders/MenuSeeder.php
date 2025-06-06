<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('menus')->insert([
            ['category_id' => 1, 'name' => 'Nasi Putih', 'price' => 3000],
            ['category_id' => 2, 'name' => 'Ayam Goreng', 'price' => 10000],
            ['category_id' => 2, 'name' => 'Telur Balado', 'price' => 5000],
            ['category_id' => 2, 'name' => 'Tempe Orek', 'price' => 3000],
            ['category_id' => 3, 'name' => 'Sayur Sop', 'price' => 3000],
            ['category_id' => 3, 'name' => 'Sayur Terong', 'price' => 3000],
            ['category_id' => 3, 'name' => 'Sayur Daun Singkong', 'price' => 3000],
            ['category_id' => 4, 'name' => 'Kopi Kapal Api', 'price' => 3000],
            ['category_id' => 4, 'name' => 'Es The Manis', 'price' => 5000],
            ['category_id' => 5, 'name' => 'Kerupuk', 'price' => 3000],
        ]);
    }
}
