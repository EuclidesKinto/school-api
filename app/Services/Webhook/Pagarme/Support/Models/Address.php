<?php

namespace App\Services\Webhook\Pagarme\Support\Models;

class Address extends Model
{
    public string $id;
    public string $line_1;
    public string $line_2;
    public string $city;
    public string $zipcode;
    public string $state;
    public string $country;
    public string $status;
    public string $created_at;
    public string $updated_at;


    public function created()
    {
        // logica para atualizar o endereço local
    }

    public function updated()
    {
        // logica para atualizar o endereço
    }


    public function deleted()
    {
        // lógica para deletar o endereço
    }
}
