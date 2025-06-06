<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                'name' => 'Makanan Berat',
                'desc' => 'Seperti nasi putih',
            ],
            [
                'name' => 'Lauk',
                'desc' => 'Seperti ayam goreng, telur dadar',
            ],
            [
                'name' => 'Sayur',
                'desc' => 'Berbagai jenis hidangan sayur',
            ],
            [
                'name' => 'Minuman',
                'desc' => 'Berbagai jenis minuman seperti kopi',
            ],
            [
                'name' => 'Cemilan',
                'desc' => 'Cocok sebagai pelengkap menu',
            ],
        ]);
    }
}
