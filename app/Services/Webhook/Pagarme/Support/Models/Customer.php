<?php

namespace App\Services\Webhook\Pagarme\Support\Models;

class Customer extends Model
{
    public string $id;
    public string $name;
    public string $document;
    public string $document_type;
    public string $email;
    public string $code;
    public string $created_at;
    public string $updated_at;
    public string $type;
    public Address $address;

    public function updated()
    {
        // logica para atualizar o status do cliente
    }

    public function created()
    {
        // logica para atualizar o status do cliente
    }
}
