<?php

namespace App\Http\Resources\Billing;

use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
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
            'type' => $this->type,
            'gateway_id' => $this->when(!is_null($this->gateway_id), $this->gateway_id),
            'brand' => $this->when(!is_null(data_get($this, 'metadata.brand')), data_get($this, 'metadata.brand')),
            'last_four_digits' => $this->when(!is_null(data_get($this, 'metadata.last_four_digits')), data_get($this, 'metadata.last_four_digits')),
            'status' => $this->when(!is_null(data_get($this, 'metadata.status')), data_get($this, 'metadata.status')),
            'exp_month' => $this->when(!is_null(data_get($this, 'metadata.exp_month')),  data_get($this, 'metadata.exp_month')),
            'exp_year' => $this->when(!is_null(data_get($this, 'metadata.exp_year')),  data_get($this, 'metadata.exp_year')),
            'holder_name' => $this->when(!is_null(data_get($this, 'metadata.holder_name')),  data_get($this, 'metadata.holder_name')),
        ];
    }
}
