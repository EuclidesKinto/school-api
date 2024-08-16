<?php

namespace App\Traits;

use App\Models\Invoice;
use App\Services\Pagarme\V2\Facades\Pagarme;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


trait HasPagarme
{

    public $customer;

    public $address;

    public $phones;

    public $subscription;

    public $plan;

    public $charges;

    public $latest_charge;

    public $latest_transaction;

    public $__invoice;

    public bool $success = false;

    public function getCharges($customer_id, $payment_method)
    {

        $response = Pagarme::get('charges', [
            'query' => [
                'customer_id' => $customer_id,
                'payment_method' => $payment_method
            ]
        ]);

        $charges = json_decode($response->getBody());

        return $charges;
    }

    public function SubscribeByBoleto($plan_id, $customer_id, $payment_method)
    {

        $subscription = Pagarme::subscribe([
            'plan_id' => $plan_id,
            'customer_id' => $customer_id,
            'payment_method' => $payment_method,
        ]);

        $pagarme_charge = Pagarme::getCharges(['code' => sprintf("%s-01", $subscription->code)])->first();

        $latest_transaction = $pagarme_charge->last_transaction;
        $pagarme_invoice = $pagarme_charge->invoice;


        $sub = $this->user->newSubscription('main',  $this->items[0]->product->productable);
        $sub->starts_at = Carbon::createFromTimeString($pagarme_charge->due_at);
        $sub->settings['pagarme_subscription_id'] = $subscription->id;
        $sub->raw = $subscription;
        $this->status = $pagarme_charge->status;
        $this->save();

        $pm = $this->payer->paymentMethods()->create([
            'type' => 'boleto',
            'user_id' => $this->payer->user_id,
            'details' => [
                'gateway_response' => $latest_transaction->gateway_response,
            ]
        ]);

        $tr = $this->payer->transactions()->create([
            'transaction_id' => $latest_transaction->id,
            'amount' => $latest_transaction->amount,
            'status' => $latest_transaction->status,
            'payment_method_id' => $pm->id,
            'user_id' => $this->payer->user_id,
            'order_id' => $this->id,
            'installments' => 1,
            'details' => $latest_transaction,
        ]);

        $this->invoices()->create([
            'payment_method_id' => $pm->id,
            'installments' => 1,
            'status' => $latest_transaction->status,
            'transaction_id' => $tr->id,
            'gateway_url' => $pagarme_invoice->url,
            'due_at' => Carbon::createFromTimeString($pagarme_invoice->due_at),
            'metadata' => $pagarme_invoice,
            'user_id' => $this->user->id,
            'gateway' => 'pagarme',
            'gateway_invoice_id' => $pagarme_invoice->id,
            'billing_profile_id' => $this->payer->id,
            'amount' => $tr->amount,
            'installments' => 1,
            'status' => $pagarme_invoice->status,
            'code' => $pagarme_invoice->code
        ]);

        if ($latest_transaction->success) {
            return true;
        } else {
            return false;
        }
    }
}
