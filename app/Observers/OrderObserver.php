<?php

namespace App\Observers;

use App\Services\ActiveCampaign\Facades\ActiveCampaign;
use App\Jobs\UpdateContactListStatusActiveCampaign;
use App\Models\Order;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Str;

class OrderObserver
{
    use DispatchesJobs;

    /**
     * Handle the Order "creating" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function creating(Order $order)
    {
        $order->status = Order::PENDING;
        $order->code = Str::random(30);
    }

    /**
     * Handle the Order "created" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        //
    }

    /**
     * Handle the Order "updated" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {

        if ($order->status == 'paid') {

            ActiveCampaign::handleContactList($order->user, '2', 'active');
            ActiveCampaign::handleContactList($order->user, '1', 'unsubscribed');
        }
    }

    /**
     * Handle the Order "deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }

    /**
     * Handle the Order "retrieved" event.
     *
     * @param  \App\Models\Order  $order
     * @return void
     */
    public function retrieved(Order $order)
    {
        $order->subtotal = $order->items->sum('amount');
        $order->total = $order->subtotal - $order->discounts->sum('amount');
        if (is_null($order->code)) {
            $order->code = Str::random(30);
        }
    }
}
