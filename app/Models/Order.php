<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'total_price' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    // Relasi ke Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relasi ke OrderItem (ini yang akan digunakan oleh Filament Repeater)
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // PENTING: Jangan ada fungsi 'menu()' dengan belongsToMany di sini.
    // Relasi ke menu dilakukan melalui orderItems().
}