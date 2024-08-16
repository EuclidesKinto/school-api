<?php

namespace App\Services\Webhook\Facades;

use Illuminate\Support\Facades\Facade;

class Webhook extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Webhook';
    }
}
