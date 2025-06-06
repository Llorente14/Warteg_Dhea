<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kategori extends Model
{
    use SoftDeletes;
    //
    protected $table = 'categories';
    protected $guarded =[];

    public function menu(){
        return $this->hasMany(Menu::class, 'id');
        
    }
}
