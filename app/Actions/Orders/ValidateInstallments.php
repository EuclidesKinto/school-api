<?php

namespace App\Actions\Orders;

use App\Exceptions\Orders\InvalidInstallments;
use App\Models\Order;
use Lorisleiva\Actions\Action;

class ValidateInstallments extends Action
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
    public function handle(Order $order, $installments)
    {
        $plan = $order->plan();
        $max_installments = collect(data_get($plan, 'settings.installments'))->max();
        if ($installments > $max_installments) {
            throw new InvalidInstallments("O número de parcelas não pode ser maior que {$max_installments}");
        }
        return true;
    }
}
