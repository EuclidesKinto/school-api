<?php

namespace App\Providers;

use App\Services\ActiveCampaign\ActiveCampaignService;
use Illuminate\Support\ServiceProvider;

class ActiveCampaignServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('ActiveCampaign', function ($app) {
            return new ActiveCampaignService(config('active.campaign.url'), config('active.campaign.key'));
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
