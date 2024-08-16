<?php

namespace App\Traits;

class HasPaymentGatewayScopes
{

    /**
     * Retorna models realizadas no gateway pagarme
     */
    public function scopePagarme($query)
    {
        return $query->where('gateway', 'pagarme');
    }

    /**
     * Retorna models realizadas no gateway stripe
     */
    public function scopeStripe($query)
    {
        return $query->where('gateway', 'stripe');
    }

    /**
     * retorna models por um determinado gateway_id
     */
    public function scopeByGatewayId($query, $gateway_id)
    {
        return $query->where('gateway_id', $gateway_id);
    }
}
