<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\SelfOrder;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/order', SelfOrder::class);