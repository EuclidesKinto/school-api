<?php

namespace App\Services\Webhook\Pagarme\Support\Models;

use Illuminate\Support\Str;
use stdClass;

class Webhook extends Model
{
    public string $id;
    public string $type;
    public string $created_at;
    public stdClass $data;

    public function model(): string
    {
        return Str::before($this->type, '.');
    }


    public function event(): string
    {
        return Str::camel(Str::after($this->type, '.'));
    }
}
