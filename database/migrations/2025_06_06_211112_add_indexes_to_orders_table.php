<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('deleted_at');
            $table->index(['status', 'deleted_at']); // composite index
        });
        
        Schema::table('customers', function (Blueprint $table) {
            $table->index('deleted_at');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['status', 'deleted_at']);
        });
        
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['deleted_at']);
        });
    }
};