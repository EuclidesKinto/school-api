<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class IuguPaymentMethod extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'iuguPaymentMethod';
    }
}
