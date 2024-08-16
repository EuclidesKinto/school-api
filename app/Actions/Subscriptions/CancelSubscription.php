<?php

namespace App\Actions\Subscriptions;

use App\Models\Subscription;
use App\Services\Pagarme\V2\Facades\Pagarme as PagarmeV2;
use App\Services\Stripe\Facades\Stripe;
use Exception;
use Lorisleiva\Actions\Action;

class CancelSubscription extends Action
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
    public function handle(Subscription $subscription)
    {
        switch ($subscription->gateway) {
            case 'stripe':
                return Stripe::subscriptions()->cancel($subscription->gateway_id);
            case 'pagarme':
                return PagarmeV2::unsubscribe($subscription->gateway_id, true);
            default:
                throw new Exception("Inavlid subscription gateway", 400);
        }
    }
}
