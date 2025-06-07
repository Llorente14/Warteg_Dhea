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

    // Relasi ke menu dilakukan melalui orderItems().
    // Relasi ke OrderItem (ini yang akan digunakan oleh Filament Repeater)
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

      public function stockUsages()
    {
        return $this->hasMany(StockUsage::class);
    }
   
    
}