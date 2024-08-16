<?php

namespace App\Http\Resources\Billing;

use App\Http\Resources\Userland\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'total' => $this->total,
            'subtotal' => $this->subtotal,
            'status' => $this->status,
            'items' => OrderItemResource::collection($this->items),
            'discounts' => DiscountResource::collection($this->discounts),
            'latest_update' => new OrderUpdateResource($this->latestUpdate),
            'transaction' => new TransactionResource($this->transaction),
        ];
    }
}
