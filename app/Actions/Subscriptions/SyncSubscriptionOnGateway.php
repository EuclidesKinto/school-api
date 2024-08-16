<?php

namespace App\Actions\Subscriptions;

use App\Models\Subscription;
use App\Services\Pagarme\V2\Facades\Pagarme as PagarmeV2;
use App\Services\Stripe\Facades\Stripe;
use Carbon\Carbon;
use Exception;
use Lorisleiva\Actions\Action;

class SyncSubscriptionOnGateway extends Action
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
        $gateway_sub = $this->retrieveFromGateway($subscription->gateway, $subscription->gateway_id);
        switch ($subscription->gateway) {
            case 'pagarme':
                $cycle = $gateway_sub->current_cycle;
                $subscription->starts_at = $cycle->start_at;
                $subscription->ends_at = $cycle->end_at;
                $subscription->settings['cycle'] = $cycle;
                $subscription->settings['next_billing_at'] = $gateway_sub->next_billing_at;
                $subscription->raw = $gateway_sub;
                if ($gateway_sub->status == 'canceled') {
                    $subscription->cancels_at = $subscription->ends_at;
                    $subscription->canceled_at = Carbon::createFromTimeString($gateway_sub->canceled_at);
                    $subscription->firemodelEvent('canceled', false);
                }
                $subscription->save();
                return $subscription;
            case 'stripe':
                $subscription->starts_at = Carbon::createFromTimestamp($gateway_sub->current_period_start);
                $subscription->ends_at = Carbon::createFromTimestamp($gateway_sub->current_period_end);
                $subscription->raw = $gateway_sub;
                if ($subscription->status == 'canceled') {
                    $subscription->cancels_at = Carbon::createFromTimestamp($gateway_sub->cancel_at);
                    $subscription->canceled_at = Carbon::createFromTimestamp($gateway_sub->canceled_at);
                    $subscription->firemodelEvent('canceled', false);
                }
                $subscription->save();
                return $subscription;
            default:
                throw new Exception("Gateway inválido!", 500);
                break;
        }
    }


    private function retrieveFromGateway($gateway, $gateway_id)
    {
        switch ($gateway) {
            case 'pagarme':
                return PagarmeV2::getSubscription($gateway_id);
            case 'stripe':
                return Stripe::subscriptions()->retrieve($gateway_id);
            default:
                throw new Exception("Gateway inválido!", 500);
                break;
        }
    }
}
