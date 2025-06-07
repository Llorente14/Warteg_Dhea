<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $casts = [
        'data' => 'array', // INI YANG PENTING
        'read_at' => 'datetime', // Laravel notification default
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
