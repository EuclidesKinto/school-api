<?php

namespace App\Actions\Orders;

use App\Actions\Subscriptions\CreateBoletoSubscription;
use App\Actions\Subscriptions\CreateCardSubscription;
use App\Actions\Subscriptions\CreatePixSubscription;
use App\Exceptions\Orders\InvalidOrderException;
use App\Models\Order;
use App\ValueClasses\OrderPaymentMethods;
use Lorisleiva\Actions\Action;

class DoCheckout extends Action
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
    public function handle(Order $order)
    {
        $order->validate();
        $order->processing();

        switch ($order->payment_method) {
            case OrderPaymentMethods::BOLETO:
                return CreateBoletoSubscription::make()->handle($order);
                break;
            case OrderPaymentMethods::PIX:
                return CreatePixSubscription::make()->handle($order);
                break;
            case OrderPaymentMethods::CARD:
                return CreateCardSubscription::make()->handle($order);
                break;
            default:
                throw new InvalidOrderException('Método de pagamento inválido!', 400);
                break;
        }
    }
}
