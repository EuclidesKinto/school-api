<?php

namespace App\Actions\Subscriptions;

use App\Models\Plan;
use App\Models\User;
use App\Services\Webhook\Pagarme\Support\Models\Subscription as PagarmeSubscription;
use Carbon\Carbon;
use Lorisleiva\Actions\Action;

class CreateLocalSubscription extends Action
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
    public function handle(User $user, Plan $plan, PagarmeSubscription $subscription)
    {
        $start_at = Carbon::createFromTimeString($subscription->start_at);
        $local_sub = $user->newSubscription('main', $plan, $start_at);
        $local_sub->gateway_id = $subscription->id;
        $local_sub->gateway = 'pagarme';
        $local_sub->raw = $subscription;
        $local_sub->is_paid = false;
        $local_sub->settings['next_billing_at'] = $subscription->next_billing_at;
        $local_sub->saveQuietly();
        return $local_sub;
    }
}
