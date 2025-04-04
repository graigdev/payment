<?php

namespace GraigDev\Payment;

use Illuminate\Support\ServiceProvider;
use GraigDev\Payment\Console\Commands\PublishModelsCommand;

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
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                PublishModelsCommand::class,
            ]);
        }

        // Publish config
        $this->publishes([
            __DIR__ . '/config/payment.php' => config_path('payment.php'),
        ], 'payment-config');

        // Publish migrations
        $this->publishes([
            __DIR__ . '/Database/migrations' => database_path('migrations'),
        ], 'payment-migrations');

        // Publish models
        $this->publishes([
            __DIR__ . '/Models' => app_path('Models/Payment'),
        ], 'payment-models');

        // Load migrations (only if not published)
        $this->loadMigrationsFrom(__DIR__ . '/Database/migrations');

        // Load routes
        $this->loadRoutesFrom(__DIR__ . '/Routes/web.php');
        
        // Load views if needed
        // $this->loadViewsFrom(__DIR__ . '/resources/views', 'payment');
    }
} 