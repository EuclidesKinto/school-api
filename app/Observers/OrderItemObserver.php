<?php

namespace App\Observers;

use App\Models\OrderItem;
use App\Models\Product;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "creating" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function creating(OrderItem $orderItem)
    {
        $product = Product::findOrFail($orderItem->product_id);
        if ($orderItem->quantity < 1) {
            $orderItem->quantity = 1;
        }
        $orderItem->amount = $orderItem->quantity * $product->price;
    }

    /**
     * Handle the OrderItem "created" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function created(OrderItem $orderItem)
    {
        //
    }

    /**
     * Handle the OrderItem "updating" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function updating(OrderItem $orderItem)
    {
        if ($orderItem->quantity < 1) {
            $orderItem->delete();
            return false;
        }
        /**
         * Essa query é diferente da de cima, porque no momento em que o evento
         * 'creating' é disparado, o relacionamento $orderItem->product ainda
         * não existe. Visto que o item ainda não foi criado no DB.
         */
        $orderItem->amount = $orderItem->product->price * $orderItem->quantity;
    }


    /**
     * Handle the OrderItem "updated" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function updated(OrderItem $orderItem)
    {
        //
    }

    /**
     * Handle the OrderItem "deleted" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function deleted(OrderItem $orderItem)
    {
        //
    }

    /**
     * Handle the OrderItem "restored" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function restored(OrderItem $orderItem)
    {
        //
    }

    /**
     * Handle the OrderItem "force deleted" event.
     *
     * @param  \App\Models\OrderItem  $orderItem
     * @return void
     */
    public function forceDeleted(OrderItem $orderItem)
    {
        //
    }
}
