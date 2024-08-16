<?php

namespace App\Observers;

use App\Services\ActiveCampaign\Facades\ActiveCampaign;
use App\Models\Subscription;
use Illuminate\Foundation\Bus\DispatchesJobs;

class SubscriptionObserver
{
    use DispatchesJobs;
    /**
     * Handle the Subscription "created" event.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return void
     */
    public function created(Subscription $subscription)
    {
        //
    }

    /**
     * Handle the Subscription "updated" event.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return void
     */
    public function updated(Subscription $subscription)
    {
        //
    }

    /**
     * Handle the Subscription "deleted" event.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return void
     */
    public function deleted(Subscription $subscription)
    {
        //
    }

    /**
     * Handle the Subscription "restored" event.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return void
     */
    public function restored(Subscription $subscription)
    {
        //
    }

    /**
     * Handle the Subscription "force deleted" event.
     *
     * @param  \App\Models\Subscription  $subscription
     * @return void
     */
    public function forceDeleted(Subscription $subscription)
    {
        //
    }

    /**
     * Handle the Subscription "canceled" event
     */
    public function canceled(Subscription $subscription)
    {
        //activate user on freemium list, and deactivates on premium list

        ActiveCampaign::handleContactList($subscription->subscriber, '1', 'active');
        ActiveCampaign::handleContactList($subscription->subscriber, '2', 'unsubscribed');
    }
}
