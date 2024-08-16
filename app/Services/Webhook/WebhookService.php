<?php

namespace App\Services\Webhook;

use App\Services\Webhook\Pagarme\Handler;

class WebhookService
{
    protected Handler $handler;

    public function __construct()
    {
        $this->handler = new Handler();
    }

    public function handle($webhook)
    {
        $this->handler->handle($webhook);
    }
}
