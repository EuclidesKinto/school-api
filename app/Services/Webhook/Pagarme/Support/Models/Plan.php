<?php

namespace App\Services\Webhook\Pagarme\Support\Models;

class Plan extends Model
{
    public string $id;
    public string $name;
    public string $description;
    public string $billing_type;
    public array $payment_methods;
    public string $statement_descriptor;
    public int $minimum_price;
    public string $interval;
    public int $interval_count;
    public string $status;
    public array $installments;
    public string $created_at;
    public string $updated_at;
    public string $deleted_at;

    public function created()
    {
        // logica para atualizar o plano local
    }

    public function updated()
    {
        // logica para atualizar o plano
    }

    public function deleted()
    {
        // logica para deletar o plano
    }
}
