<?php

namespace App\Observers;

use App\Models\Coupon;
use App\Models\Discount;
use App\Models\Order;

class DiscountObserver
{
    /**
     * Handle the Order "retrieved" event.
     *
     * @param  \App\Models\Discount  $discount
     * @return void
     */
    public function retrieved(Discount $discount)
    {
        //
    }

    /**
     * Handle the Discount "created" event.
     *
     * @param  \App\Models\Discount  $discount
     * @return void
     */
    public function created(Discount $discount)
    {
        $order = Order::findOrFail($discount->order_id);
        $coupon = Coupon::findOrFail($discount->coupon_id);
        if ($coupon->type == 'flat') {
            $discount->amount = $coupon->value;
        } else {
            $discount->amount = $order->subtotal * ($coupon->value / 100);
        }
        $discount->saveQuietly();
    }

    /**
     * Handle the Discount "updated" event.
     *
     * @param  \App\Models\Discount  $discount
     * @return void
     */
    public function updated(Discount $discount)
    {
        //
    }

    /**
     * Handle the Discount "deleted" event.
     *
     * @param  \App\Models\Discount  $discount
     * @return void
     */
    public function deleted(Discount $discount)
    {
        //
    }

    /**
     * Handle the Discount "restored" event.
     *
     * @param  \App\Models\Discount  $discount
     * @return void
     */
    public function restored(Discount $discount)
    {
        //
    }

    /**
     * Handle the Discount "force deleted" event.
     *
     * @param  \App\Models\Discount  $discount
     * @return void
     */
    public function forceDeleted(Discount $discount)
    {
        //
    }
}
