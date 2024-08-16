<?php

namespace App\Services\Billing;

use App\Models\BillingProfile;
use App\Models\Order;
use App\ValueClasses\OrderPaymentMethods;
use Illuminate\Support\Arr;
use stdClass;

class PaymentService
{

    protected array $subscription = [];

    protected array $customer = [];

    protected array $order = [];


    public function subscribe(Order $order, $card_id)
    {
        $subscription = null;
        switch ($order->payment_method) {
            case OrderPaymentMethods::BOLETO:
                $subscription = $this->mountSubscription($order);
                break;
            case OrderPaymentMethods::CARD:
                $subscription = $this->mountSubscription($order, $card_id);
                break;
            case OrderPaymentMethods::PIX:
                $subscription = $this->mountOrder($order);
                break;
        }
    }

    public function createSubscription($subscription)
    {

        return $this;
    }

    private function mountSubscription(Order $order, $card_id = null)
    {
        $plan = $order->plan();
        $customer = $order->payer;

        $this->subscription['plan_id'] = $plan->pagarme_plan_id;
        $this->subscription['customer_id'] = $customer->metadata['pagarme_id'];

        switch ($order->payment_method) {
            case OrderPaymentMethods::BOLETO:
                $this->subscription['payment_method'] = 'boleto';
                $this->subscription['installments'] = 1;
                break;
            case OrderPaymentMethods::CARD:
                $this->subscription['installments'] = $order->installments;
                $this->subscription['payment_method'] = 'credit_card';
                $this->subscription['card_id'] = $card_id;
                break;
        }
        return $this;
    }

    private function mountOrder(Order $order)
    {
        $this->order = [
            'customer' => $this->mountCustomer($order->payer),
            'items' => $this->mountItems($order),
            'payments' => [
                [
                    'payment_method' => 'pix',
                    'pix' => [
                        'expires_in' => 7200,
                        'amount' => $order->total,
                    ]
                ]
            ]
        ];

        return $this;
    }


    private function mountCustomer(BillingProfile $payer)
    {
        return Arr::undot([
            'name' => $payer->name,
            'email' => $payer->email,
            'document' => $payer->document,
            'document_type' => $payer->document_type,
            'type' => 'individual',
            'phones.home_phone.country_code' => data_get($payer, 'phones.home.ddi'),
            'phones.home_phone.area_code' => data_get($payer, 'phones.home.ddd'),
            'phones.home_phone.number' => data_get($payer, 'phones.home.number'),
        ]);
    }


    private function mountItems(Order $order)
    {
        $plan = $order->plan();
        return Arr::undot([
            "0.amount" => $plan->cents_price,
            "0.description" => $plan->name,
            "0.quantity" => 1,
            "0.code" => $plan->id,
            "0.pricing_scheme.price" => $plan->cents_price,
            "0.pricing_scheme.scheme_type" => "unit",
        ]);
    }
}
