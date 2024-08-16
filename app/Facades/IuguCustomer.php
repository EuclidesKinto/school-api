<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class IuguCustomer extends Facade
{

    protected static function getFacadeAccessor()
    {
        return 'iuguCustomer';
    }
}
