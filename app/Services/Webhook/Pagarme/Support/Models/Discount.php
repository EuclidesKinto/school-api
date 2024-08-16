<?php

namespace App\Services\Webhook\Pagarme\Support\Models;

class Discount extends Model
{
    public string $id;
    public string $value;
    public string $discount_type;
    public string $cycles;

    public function created()
    {
        // logica para atualizar o desconto
    }

    public function deleted()
    {
        // lógica para remover o desconto do pedido
    }
}
