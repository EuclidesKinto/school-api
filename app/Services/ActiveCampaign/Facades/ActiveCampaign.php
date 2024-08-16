<?php

namespace App\Services\ActiveCampaign\Facades;

use Illuminate\Support\Facades\Facade;

class ActiveCampaign extends Facade
{

    /**
     * Get the registered name of the component
     * 
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'ActiveCampaign';
    }
}
