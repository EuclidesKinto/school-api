<?php

namespace App\Http\Resources\Billing;

use App\ValueClasses\OrderPaymentMethods;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'status' => $this->status,
            'amount' => $this->amount,
            'payment_method' => $this->paymentMethod->type,
            $this->mergeWhen($this->paymentMethod->type == OrderPaymentMethods::BOLETO, [
                'pdf' => data_get($this, 'details.pdf'),
                'url' => data_get($this, 'details.url'),
                'line' => data_get($this, 'details.line'),
                'barcode' => data_get($this, 'details.barcode'),
                'qr_code' => data_get($this, 'details.qr_code'),
                'due_at' => data_get($this, 'details.due_at'),
            ]),
            $this->mergeWhen($this->paymentMethod->type == OrderPaymentMethods::PIX, [
                'expires_at' => $this->when(!is_null(data_get($this, 'details.expires_at')), data_get($this, 'details.expires_at')),
                'qr_code' => $this->when(!is_null(data_get($this, 'details.qr_code')), data_get($this, 'details.qr_code')),
                'qr_code_url' => $this->when(!is_null(data_get($this, 'details.qr_code_url')), data_get($this, 'details.qr_code_url')),
                'gateway_response' =>  $this->when((int)data_get($this, 'gateway_response.code') >= 400, data_get($this, 'gateway_response')),
            ]),
        ];
    }
}
