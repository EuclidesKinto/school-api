<?php

namespace App\Actions\Orders;

use App\Models\PaymentMethod;
use App\Services\Pagarme\V2\Facades\Pagarme as PagarmeV2;
use Lorisleiva\Actions\Action;

class DeleteCard extends Action
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
    public function handle(PaymentMethod $pm)
    {
        $payer = $pm->billingProfile;
        $card = PagarmeV2::deleteCard($payer->metadata['pagarme_id'], $pm->gateway_id);
        return $card;
    }
}
