<?php

namespace App\Services\Webhook\Pagarme\Support\Models;

use stdClass;

class Transaction extends Model
{

    public string $id;
    public string $status;
    public bool $success;
    public int $installments;
    public string $created_at;
    public string $updated_at;
    public int $amount;
    public string $statement_descriptor;
    public string $operation_type;
    public Card $card;
    public stdClass $gateway_response;
    public stdClass $antifraud_response;
}
