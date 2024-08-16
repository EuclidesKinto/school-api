<?php

namespace App\Actions\Orders;

use App\Exceptions\Orders\InvalidOrderException;
use App\Models\Order;
use Lorisleiva\Actions\Action;

class ValidateOrder extends Action
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
        // Execute the action.
        if ($order->items()->count() > 1) {
            throw new InvalidOrderException("O pedido deve conter apenas um plano", 412);
        }
    }
}
