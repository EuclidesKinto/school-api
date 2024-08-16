<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Stripe\StripeService;

class StripeServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton('Stripe', function ($app) {
            return new StripeService(config('stripe.stripe_secret'));
        });
    }

    public function boot()
    {
        //
    }
}
