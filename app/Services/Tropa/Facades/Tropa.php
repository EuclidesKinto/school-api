<?php

namespace App\Services\Tropa\Facades;

use Illuminate\Support\Facades\Facade;

class Tropa extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Tropa';
    }
}
