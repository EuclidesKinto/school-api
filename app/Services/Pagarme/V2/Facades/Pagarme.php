<?php

namespace App\Services\Pagarme\V2\Facades;

use Illuminate\Support\Facades\Facade;

class Pagarme extends Facade
{

    /**
     * Get the registered name of the component
     * 
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'PagarmeV2';
    }
}
