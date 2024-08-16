<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Tropa\TropaService;

class TropaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Tropa', function ($app) {
            return new TropaService();
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
