<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Terhubung ke tabel 'orders'
            $table->foreignId('menu_id')->constrained()->onDelete('cascade'); // Terhubung ke tabel 'menus'
            $table->integer('quantity'); // Kuantitas menu yang dipesan
            $table->decimal('price', 12, 2); // Harga menu saat dipesan (snapshot)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};