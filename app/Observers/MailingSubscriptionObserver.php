<?php

namespace App\Observers;

use App\Models\MailingSubscription;
use Illuminate\Queue\Jobs\Job;

class MailingSubscriptionObserver
{
    /**
     * Handle the MailingSubscription "created" event.
     *
     * @param  \App\Models\MailingSubscription  $MailingSubscription
     * @return void
     */
    public function created(MailingSubscription $MailingSubscription)
    {
        // Job::dispatch(new \App\Jobs\Mailing\SubscribeUserToMailingList($MailingSubscription));
    }

    /**
     * Handle the MailingSubscription "updated" event.
     *
     * @param  \App\Models\MailingSubscription  $MailingSubscription
     * @return void
     */
    public function updated(MailingSubscription $MailingSubscription)
    {
        //
    }

    /**
     * Handle the MailingSubscription "deleted" event.
     *
     * @param  \App\Models\MailingSubscription  $MailingSubscription
     * @return void
     */
    public function deleted(MailingSubscription $MailingSubscription)
    {
        //
    }

    /**
     * Handle the MailingSubscription "restored" event.
     *
     * @param  \App\Models\MailingSubscription  $MailingSubscription
     * @return void
     */
    public function restored(MailingSubscription $MailingSubscription)
    {
        //
    }

    /**
     * Handle the MailingSubscription "force deleted" event.
     *
     * @param  \App\Models\MailingSubscription  $MailingSubscription
     * @return void
     */
    public function forceDeleted(MailingSubscription $MailingSubscription)
    {
        //
    }
}
