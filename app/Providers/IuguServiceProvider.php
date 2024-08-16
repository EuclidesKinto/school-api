<?php

namespace App\Providers;

use App\Services\Iugu\IuguInvoice;
use App\Services\Iugu\IuguCharge;
use App\Services\Iugu\IuguCustomer;
use App\Services\Iugu\IuguPaymentMethod;
use App\Services\Iugu\IuguSubscription;
use Illuminate\Support\ServiceProvider;

class IuguServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('iuguCustomer', function ($app) {
            return new IuguCustomer();
        });

        $this->app->singleton('iuguPaymentMethod', function ($app) {
            return new IuguPaymentMethod();
        });

        $this->app->singleton('iuguSubscription', function ($app) {
            return new IuguSubscription();
        });

        $this->app->singleton('iuguCharge', function ($app) {
            return new IuguCharge();
        });

        $this->app->singleton('iuguInvoice', function ($app) {
            return new IuguInvoice();
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
