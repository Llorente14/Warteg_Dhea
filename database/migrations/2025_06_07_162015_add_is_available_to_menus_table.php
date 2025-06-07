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
        Schema::table('menus', function (Blueprint $table) {
            // Tambahkan kolom boolean 'is_available' dengan nilai default true
            $table->boolean('is_available')->default(true)->after('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            // Hapus kolom 'is_available' jika migrasi di-rollback
            $table->dropColumn('is_available');
        });
    }
};