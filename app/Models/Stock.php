<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_available' => 'boolean',
        
    ];

     public function stockUsages()
    {
        return $this->hasMany(StockUsage::class);
    }
}
