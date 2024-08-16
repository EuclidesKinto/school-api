<?php

namespace App\Services\Webhook\Pagarme\Support\Models;

use Illuminate\Support\Facades\Log;

class Invoice extends Model
{
    public string $id;
    public string $code;
    public string $url;
    public int $amount;
    public string $payment_method;
    public string $status;
    public int $installments;
    public string $due_at;

    public function created()
    {
        //
    }

    public function updated()
    {
        // logica para atualizar o status do pedido
    }

    public function paymentFailed()
    {
        // logica para atualizar o status do pedido
    }

    public function paid()
    {
        // logica para atualizar a fatura
    }

    public function canceled()
    {
        // lógica para cancelar a fatura
    }
}
