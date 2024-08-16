<?php

namespace App\Services\Webhook\Pagarme\Support\Models;

class Card extends Model
{
    public string $id;
    public string $last_four_digits;
    public string $brand;
    public string $holder_name;
    public int $exp_month;
    public int $exp_year;
    public string $type;
    public string $status;
    public string $created_at;
    public string $updated_at;
    public string $deleted_at;
    public Address $billing_address;


    public function created()
    {
        // logica para atualizar o cartão local
    }

    public function updated()
    {
        // logica para atualizar o cartão
    }

    public function deleted()
    {
        // lógica para deletar o cartão
    }

    public function expired()
    {
        // lógica para deletar o cartão
    }
}
