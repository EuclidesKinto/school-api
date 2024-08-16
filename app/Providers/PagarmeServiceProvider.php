<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Pagarme\PagarmeService;
use App\Services\Pagarme\V2\PagarmeService as PagarmeServiceV2;

class PagarmeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Pagarme', function ($app) {
            return new PagarmeService(config('pagarme.secret_key'));
        });

        $this->app->singleton('PagarmeV2', function ($app) {
            return new PagarmeServiceV2();
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
