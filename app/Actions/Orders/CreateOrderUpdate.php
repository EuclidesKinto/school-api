<?php

namespace App\Actions\Orders;

use App\Models\Order;
use Carbon\Carbon;
use Lorisleiva\Actions\Action;

class CreateOrderUpdate extends Action
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
    public function handle()
    {
        // Execute the action.
    }

    public function processing(Order $order)
    {
        $order->status = Order::PROCESSING;
        $order->charge()->update(['status' => Order::PROCESSING]);
        $order->invoice()->update(['status' => Order::PROCESSING]);
        $order->transaction()->update(['status' => Order::PROCESSING]);
        $order->saveQuietly();
        $order->updates()->create([
            'status' => Order::PROCESSING,
            'description' => 'Pedido em processamento.',
        ]);
        return true;
    }

    public function paymentPending(Order $order)
    {
        $order->status = Order::PENDING_PAYMENT;
        $order->charge()->update(['status' => Order::PENDING_PAYMENT]);
        $order->invoice()->update(['status' => Order::PENDING_PAYMENT]);
        $order->transaction()->update(['status' => Order::PENDING_PAYMENT]);
        $order->saveQuietly();
        $order->updates()->create([
            'status' => Order::PENDING_PAYMENT,
            'description' => 'Pedido em processamento.',
        ]);
        return true;
    }

    public function paid(Order $order)
    {
        $order->status = Order::PAID;
        $order->paid_at = Carbon::now();
        $order->charge()->update(['status' => Order::PAID, 'paid_at' => $order->paid_at]);
        $order->invoice()->update(['status' => Order::PAID, 'paid_at' => $order->paid_at]);
        $order->charge->subscription()->update(['is_paid' => true]);
        $order->transaction()->update(['status' => Order::PAID]);
        $order->save();
        $order->updates()->create([
            'status' => Order::PAID,
            'description' => 'Pedido pago.',
        ]);

        return true;
    }

    public function canceled(Order $order)
    {
        $order->status = Order::CANCELED;
        $order->charge()->update(['status' => Order::CANCELED]);
        $order->invoice()->update(['status' => Order::CANCELED]);
        $order->transaction()->update(['status' => Order::CANCELED]);
        $order->saveQuietly();
        $order->updates()->create([
            'status' => Order::CANCELED,
            'description' => 'Pedido cancelado.',
        ]);

        return true;
    }

    public function paymentFailed(Order $order)
    {
        $order->status = Order::PAYMENT_FAILED;
        $order->charge()->update(['status' => Order::PAYMENT_FAILED]);
        $order->invoice()->update(['status' => Order::PAYMENT_FAILED]);
        $order->transaction()->update(['status' => Order::PAYMENT_FAILED]);
        $order->saveQuietly();
        $order->updates()->create([
            'status' => Order::PAYMENT_FAILED,
            'description' => 'Pedido falhou.',
        ]);

        return true;
    }

    public function refunded(Order $order)
    {
        $order->status = Order::REFUNDED;
        $order->refunded_at = Carbon::now();
        $order->charge()->update(['status' => Order::REFUNDED, 'refunded_at' => $order->refunded_at]);
        $order->invoice()->update(['status' => Order::REFUNDED, 'refunded_at' => $order->refunded_at]);
        $order->transaction()->update(['status' => Order::REFUNDED]);
        $order->charge->refunded_at = $order->refunded_at;
        $order->invoice->refunded_at = $order->refunded_at;
        $order->saveQuietly();
        $order->updates()->create([
            'status' => Order::REFUNDED,
            'description' => 'Pagamento reembolsado.',
        ]);

        return true;
    }

    public function closed(Order $order)
    {
        $order->status = Order::CLOSED;
        $order->saveQuietly();
        $order->updates()->create([
            'status' => Order::CLOSED,
            'description' => 'Pedido finalizado.',
        ]);

        return true;
    }
}
