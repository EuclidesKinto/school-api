<?php

namespace App\Http\Resources\Billing;

use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionResource extends JsonResource
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
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'cancels_at' => $this->when(!is_null($this->cancels_at), $this->cancels_at),
            'canceled_at' => $this->when(!is_null($this->canceled_at), $this->canceled_at),
            'paid' => $this->isPaid(),
            'status' => $this->status,
            'gateway' => $this->gateway,
            'plan' => new PlanResource($this->plan),
            'billing_portal_url' => $this->when(!is_null($this->billing_portal_url), $this->billing_portal_url),
            'payment_method' => $this->when(!is_null($this->payment_method), new PaymentMethodResource($this->payment_method)),
        ];
    }
}
