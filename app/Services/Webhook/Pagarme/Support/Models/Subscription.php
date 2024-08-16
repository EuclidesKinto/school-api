<?php

namespace App\Services\Webhook\Pagarme\Support\Models;

use App\Models\Subscription as ModelsSubscription;
use stdClass;

class Subscription extends Model
{
    public string $id;
    public string $code;
    public string $payment_method;
    public string $currency;
    public string $status;
    public string $start_at;
    public string $interval;
    public int $interval_count;
    public int $minimum_price;
    public string $billing_type;
    public string $billing_day;
    public string $statement_descriptor;
    public int $installments;
    public string $created_at;
    public string $updated_at;
    public string $canceled_at;
    public string $next_billing_at;
    public stdClass $customer;
    public stdClass $card;
    public stdClass $plan;
    public array $discounts;
    public stdClass $current_period;
    public stdClass $current_cycle;
    public string $card_id;
    public string $customer_id;
    public string $plan_id;
    public stdClass $metadata;



    protected $property_map = [
        'id' => 'id',
        'code' => 'code',
        'payment_method' => 'payment_method',
        'description' => 'description',
        'currency' => 'currency',
        'status' => 'status',
        'start_at' => 'start_at',
        'interval' => 'interval',
        'interval_count' => 'interval_count',
        'minimum_price' => 'minimum_price',
        'billing_type' => 'billing_type',
        'billing_day' => 'billing_day',
        'statement_descriptor' => 'statement_descriptor',
        'installments' => 'installments',
        'created_at' => 'created_at',
        'updated_at' => 'updated_at',
        'canceled_at' => 'canceled_at',
        'next_billing_at' => 'next_billing_at',
        'customer' => 'customer',
        'card' => 'card',
        'plan' => 'plan',
        'discounts' => 'discounts',
        'current_period' => 'current_period',
        'current_cycle' => 'current_cycle'
    ];

    public function activated()
    {
        // logica para atualizar o status da subscription
    }

    public function created()
    {
        // logica para atualizar o status do pedido local
    }

    public function updated()
    {
        // logica para atualizar o status do pedido
    }

    public function canceled()
    {
        $subscription = ModelsSubscription::where('gateway', 'pagarme')->where('gateway_id', $this->id)->firstOrFail();
        $subscription->raw = $this;
        $subscription->cancel();
        return true;
    }
}
