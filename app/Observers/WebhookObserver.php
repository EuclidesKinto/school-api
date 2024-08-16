<?php

namespace App\Observers;

use App\Models\Webhook;
use Carbon\Carbon;

class WebhookObserver
{

    /**
     * Handle the Webhook "creating" event.
     *
     * @param  \App\Models\Webhook  $webhook
     * @return void
     */
    public function creating(Webhook $webhook)
    {
        if (is_string($webhook->timestamp)) {
            $webhook->timestamp = Carbon::createFromTimeString($webhook->timestamp);
        }
    }

    /**
     * Handle the Webhook "created" event.
     *
     * @param  \App\Models\Webhook  $webhook
     * @return void
     */
    public function created(Webhook $webhook)
    {
        //
    }

    /**
     * Handle the Webhook "updated" event.
     *
     * @param  \App\Models\Webhook  $webhook
     * @return void
     */
    public function updated(Webhook $webhook)
    {
        //
    }

    /**
     * Handle the Webhook "deleted" event.
     *
     * @param  \App\Models\Webhook  $webhook
     * @return void
     */
    public function deleted(Webhook $webhook)
    {
        //
    }

    /**
     * Handle the Webhook "restored" event.
     *
     * @param  \App\Models\Webhook  $webhook
     * @return void
     */
    public function restored(Webhook $webhook)
    {
        //
    }

    /**
     * Handle the Webhook "force deleted" event.
     *
     * @param  \App\Models\Webhook  $webhook
     * @return void
     */
    public function forceDeleted(Webhook $webhook)
    {
        //
    }
}
