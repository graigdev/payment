<?php

namespace GraigDev\Payment;

use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Merge config
        $this->mergeConfigFrom(__DIR__ . '/config/payment.php', 'payment');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config
        $this->publishes([
            __DIR__ . '/config/payment.php' => config_path('payment.php'),
        ], 'payment-config');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        
        // Load views if needed
        // $this->loadViewsFrom(__DIR__ . '/resources/views', 'payment');
    }
} 