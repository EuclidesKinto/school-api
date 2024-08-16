<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class InvoiceObserver
{
    /**
     * Handle the Invoice "created" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function created(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "updated" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function updated(Invoice $invoice)
    {

        if ($invoice->status == 'paid') {

            $subscription = Subscription::where('user_id', '=', $invoice->user_id)->first();

            $subscription->plan_id = $invoice->plan_id;
            $subscription->status = 'active';
            $subscription->expires_at = Carbon::now()->addYear();
            $subscription->started_at = Carbon::now();
            $subscription->renewable = 1;

            $subscription->save();
        } else {

            $plan = DB::table('plans')->where('identifier', 'freemium')->get();

            $subscription = Subscription::where('user_id', '=', $invoice->user_id)->first();

            $subscription->plan_id = $plan[0]->id;
            $subscription->status = 'active';
            $subscription->expires_at = Carbon::maxValue();
            $subscription->started_at = Carbon::now();
            $subscription->renewable = null;

            $subscription->save();
        }
    }

    /**
     * Handle the Invoice "deleted" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function deleted(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "restored" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function restored(Invoice $invoice)
    {
        //
    }

    /**
     * Handle the Invoice "force deleted" event.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return void
     */
    public function forceDeleted(Invoice $invoice)
    {
        //
    }
}