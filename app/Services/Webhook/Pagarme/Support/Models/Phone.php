<?php

namespace App\Services\Webhook\Pagarme\Support\Models;

class Phone extends Model
{
    public string $country_code;
    public string $area_code;
    public string $number;

    protected $property_map = [
        'ddi' => 'country_code',
        'ddd' => 'area_code',
        'number' => 'number'
    ];
}
