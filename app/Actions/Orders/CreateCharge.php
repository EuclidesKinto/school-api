<?php

namespace App\Actions\Orders;

use App\Models\Charge;
use App\Models\Order;
use App\Models\Subscription;
use App\Services\Webhook\Pagarme\Support\Models\Charge as PagarmeCharge;
use Lorisleiva\Actions\Action;

class CreateCharge extends Action
{
    /**
     * Determine if the user is authorized to make this action.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Execute the action and return a result.
     *
     * @return mixed
     */
    public function handle(Order $order, Subscription $subscription, PagarmeCharge $pagarme_charge = null)
    {
        $charge = Charge::create([
            'amount' => $order->total,
            'currency' => 'BRL',
            'order_id' => $order->id,
            'payer_id' => $order->payer->id,
            'user_id' => $order->user_id,
            'subscription_id' => $subscription->id,
            'payment_method_id' => $order->payment_method_id,
            'payment_method' => $order->payment_method,
            'gateway' => $subscription->gateway,
            'gateway_code' => sprintf("%s-%02d", $order->code, 1),
            'gateway_payer_id' => $order->payer->metadata['pagarme_id'],
            'status' => $order->status,
            'due_at' => $order->isPaid() ? $order->paid_at : null,
            'paid_at' => $order->isPaid() ? $order->paid_at : null,
        ]);
        // caso seja informada uma cobrança da pagarme (pedidos PIX)
        if ($pagarme_charge) {
            $charge->gateway_id = $pagarme_charge->id;
            $charge->save();
        }
        return $charge;
    }
}
