<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Webhook\WebhookService;

class WebhookServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Webhook', function ($app) {
            return new WebhookService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
