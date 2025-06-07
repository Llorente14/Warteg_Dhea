<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Order;
use App\Observers\OrderObserver;
use Illuminate\Notifications\DatabaseNotification;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {   
        DatabaseNotification::booting(function (DatabaseNotification $notification) {
            $notification->mergeCasts([
                'data' => 'array',
            ]);
        });
        //Mendafatarkan observer Order
        Order::observe(OrderObserver::class);
    }
}
