<?php

namespace App\Services\Webhook\Pagarme\Support\Models;

use Illuminate\Support\Arr;

class Model
{

    protected $property_map = [];

    public function __construct(array $attributes = [])
    {
        $this->fill($attributes);
    }

    public function fill(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
        return $this;
    }

    public function setAttribute($key, $value)
    {
        if (!is_null($value)) {
            if ($this->hasPropertyMap() && Arr::has($this->property_map, $key)) {
                $property = $this->property_map[$key];
                $this->{$property} = $value;
            } else if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        return $this;
    }


    public function hasPropertyMap()
    {
        if (count($this->property_map) > 0) {
            return true;
        }
        return false;
    }
}
