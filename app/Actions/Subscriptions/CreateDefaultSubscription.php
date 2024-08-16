<?php

namespace App\Actions\Subscriptions;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Lorisleiva\Actions\Action;

class CreateDefaultSubscription extends Action
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
    public function handle(User $user)
    {
        $free_plan = Plan::where('identifier', 'free')->first();
        if (is_null($user->subscription_id)) {
            //$sub = $user->newSubscription('main', $free_plan, Carbon::now());
            $sub = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $free_plan->id,
                'status' => 'active',
                'description'=>'Plano Gratuito',
                'started_at' => Carbon::now(),
                'will_end_at' => null,
            ]);
            $user->subscription_id = $sub->id;
            $user->saveQuietly();
        }
    }
}
