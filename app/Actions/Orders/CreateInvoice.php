<?php

namespace App\Actions\Orders;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\Webhook\Pagarme\Support\Models\Charge as PagarmeCharge;
use Lorisleiva\Actions\Action;

class CreateInvoice extends Action
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
    public function handle(Order $order, $subscription, $charge, PagarmeCharge $pagarme_charge = null)
    {
        $invoice = Invoice::create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'billing_profile_id' => $order->payer_id,
            'payment_method_id' => $order->payment_method_id,
            'gateway' => $charge->gateway,
            'code' => sprintf("%s-%02d", $order->code, 1),
            'status' => $order->status,
            'due_at' => $order->isPaid() ? $order->paid_at : null,
            'paid_at' => $order->isPaid() ? $order->paid_at : null,
            'subscription_id' => $subscription->id,
            'payment_method_id' => $order->payment_method_id,
            'subtotal' => $order->subtotal,
            'total' => $order->total,
            'total_discount' => $order->total - $order->subtotal,
            'total_increment' => 0,
        ]);

        $transaction = Transaction::create([
            'invoice_id' => $invoice->id,
            'payer_id' => $order->payer_id,
            'user_id' => $order->user_id,
            'payment_method_id' => $order->payment_method_id,
            'charge_id' => $order->charge->id,
            'order_id' => $order->id,
            'gateway' => $charge->gateway,
            'amount' => $order->total,
            'currency' => 'BRL',
            'status' => $order->status,
            'paid_at' => $order->isPaid() ? $order->paid_at : null,
        ]);

        if ($pagarme_charge) {
            $transaction->gateway_id = data_get($pagarme_charge, 'last_transaction.id');
            $transaction->details = $pagarme_charge->last_transaction;
            $transaction->save();
        }

        $order->charge->update(['transaction_id' => $transaction->id, 'invoice_id' => $invoice->id]);
        $invoice->transaction_id = $transaction->id;
        $invoice->saveQuietly();
        return $invoice;
    }
}
