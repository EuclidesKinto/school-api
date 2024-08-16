<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway', // gateway que gerou o webhook ['pagarme', 'stripe']
        'webhook_id', // id do webhook no pagarme ou stripe
        'model', // model do objeto que gerou o webhook ['charge', 'subscription', 'invoice', 'order' ...]
        'event', // evento que o webhook foi disparado
        'timestamp', // created_at do webhook no pagarme ou stripe
        'data', // o campo data do webhook no pagarme ou stripe
        'raw_data', // o webhook recebido do pagarme ou stripe inteiro
        'status', // status do webhook localmente ['received', 'processed', 'failed']
    ];

    protected $casts = [
        'data' => 'object',
        'raw_data' => 'object',
    ];


    const WEBHOOK_MODELS = [
        'charge' => Charge::class,
        'subscription' => Subscription::class,
        'invoice' => Invoice::class,
        'order' => Order::class,
    ];
}
