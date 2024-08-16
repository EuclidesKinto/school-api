<?php

namespace App\Services\Webhook\Pagarme\Support\Models;

use Illuminate\Support\Facades\Log;
use stdClass;

class Order extends Model
{
    public string $id;
    public string $code;
    public stdClass $customer;
    public string $customer_id;
    public array $items;
    public array $payments;
    public array $charges;
    public bool $closed;
    public string $status;
    public string $created_at;
    public string $updated_at;

    public function created()
    {
        //
    }

    public function canceled()
    {
        Log::debug('webservice::order:canceled', [$this]);
    }

    public function paid()
    {
        Log::debug('webservice::order:paid', [$this]);
    }

    public function updated()
    {
        Log::debug('webservice::order:updated', [$this]);
    }

    public function paymentFailed()
    {
        Log::debug('webservice::order:paymentFailed', [$this]);
    }

    public function closed()
    {
        Log::debug('webservice::order:closed', [$this]);
    }

    public function processing()
    {
        Log::debug('webservice::order:processing', [$this]);
    }

    public function failed()
    {
        Log::debug('webservice::order:failed', [$this]);
    }

    public function overpaid()
    {
        Log::debug('webservice::order:overpaid ', [$this]);
    }

    public function underpaid()
    {
        Log::debug('webservice::order:underpaid', [$this]);
    }
}
