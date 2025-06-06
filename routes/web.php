<?php

use Illuminate\Support\Facades\Route;

use App\Livewire\SelfOrder; // Asumsikan nama komponennya SelfOrder
use App\Livewire\ViewOrder;
use App\Livewire\Payment;
Route::get('/order', SelfOrder::class)->name('self-order');
Route::get('/view-orders', ViewOrder::class);
Route::get('/payment', Payment::class);
