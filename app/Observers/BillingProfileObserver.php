<?php

namespace App\Observers;

use App\Models\BillingProfile;

class BillingProfileObserver
{
    /**
     * Handle the BillingProfile "created" event.
     *
     * @param  \App\Models\BillingProfile  $billingProfile
     * @return void
     */
    public function created(BillingProfile $billingProfile)
    {
        //
    }

    /**
     * Handle the BillingProfile "updated" event.
     *
     * @param  \App\Models\BillingProfile  $billingProfile
     * @return void
     */
    public function updated(BillingProfile $billingProfile)
    {
        //
    }

    /**
     * Handle the BillingProfile "deleted" event.
     *
     * @param  \App\Models\BillingProfile  $billingProfile
     * @return void
     */
    public function deleted(BillingProfile $billingProfile)
    {
        //
    }

    /**
     * Handle the BillingProfile "restored" event.
     *
     * @param  \App\Models\BillingProfile  $billingProfile
     * @return void
     */
    public function restored(BillingProfile $billingProfile)
    {
        //
    }

    /**
     * Handle the BillingProfile "force deleted" event.
     *
     * @param  \App\Models\BillingProfile  $billingProfile
     * @return void
     */
    public function forceDeleted(BillingProfile $billingProfile)
    {
        //
    }
}
