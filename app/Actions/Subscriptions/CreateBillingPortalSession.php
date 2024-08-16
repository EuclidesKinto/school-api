<?php

namespace App\Actions\Subscriptions;

use App\Services\Stripe\Facades\Stripe;
use Lorisleiva\Actions\Action;

class CreateBillingPortalSession extends Action
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
     * Create a new Stripe billing portal's session for the specified customer
     *
     * @param string $customer_id
     * @return mixed
     */
    public function handle($customer_id)
    {
        $bp_session = Stripe::billingPortal()->sessions->create([
            'customer' => $customer_id,
            'locale' => 'pt-BR',
            'return_url' => sprintf("%s/profile/billing", config('app.frontend_url'))
        ]);

        return $bp_session;
    }
}
