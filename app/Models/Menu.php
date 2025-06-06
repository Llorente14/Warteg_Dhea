<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Menu extends Model
{
    use SoftDeletes;
    //
    protected $guarded = [];

   public function category()
    {
        return $this->belongsTo(Kategori::class);
    }

     public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
