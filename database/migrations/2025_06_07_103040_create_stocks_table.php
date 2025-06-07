<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // ex: "Sendok Plastik", "Kotak Styrofoam"
            $table->enum('type', ['alat masak', 'alat makan', 'kemasan takeaway']);
            $table->integer('quantity')->default(0);
            $table->boolean('is_available')->default(true); // cek apakah tersedia
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
