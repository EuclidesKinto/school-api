<?php

namespace App\Services\Webhook\Pagarme;

use JsonMapper\JsonMapperFactory;
use App\Services\Webhook\Pagarme\Support\Models\Webhook as WebhookModel;
use App\Services\Webhook\Pagarme\Support\Models\Address;
use App\Services\Webhook\Pagarme\Support\Models\Card;
use App\Services\Webhook\Pagarme\Support\Models\Charge;
use App\Services\Webhook\Pagarme\Support\Models\Customer;
use App\Services\Webhook\Pagarme\Support\Models\Discount;
use App\Services\Webhook\Pagarme\Support\Models\Invoice;
use App\Services\Webhook\Pagarme\Support\Models\Order;
use App\Services\Webhook\Pagarme\Support\Models\Plan;
use App\Services\Webhook\Pagarme\Support\Models\Subscription;
use Illuminate\Support\Facades\Log;
use stdClass;

class Handler
{

    private $mapper;

    private $parsed_webhook;

    public function __construct()
    {
        $this->mapper = (new JsonMapperFactory())->bestFit();
    }

    public function handle($webhook)
    {
        $webhookModel = new WebhookModel();
        $this->mapper->mapObjectFromString(json_encode($webhook), $webhookModel);
        $this->parsed_webhook = $this->parse($webhookModel);
        $this->processEvent($webhookModel->event());
    }

    private function parse(WebhookModel $webhook)
    {
        if (method_exists($this, $webhook->model())) {
            return $this->{$webhook->model()}($webhook->data);
        }
        return null;
    }

    private function processEvent($event)
    {
        if (method_exists($this->parsed_webhook, $event)) {
            return $this->parsed_webhook->{$event}();
        }
        return null;
    }


    private function address($data): Address
    {
        $address = new Address();
        $this->mapper->mapObjectFromString(json_encode($data), $address);
        return $address;
    }

    private function card($data): Card
    {
        $card = new Card();
        $this->mapper->mapObjectFromString(json_encode($data), $card);
        return $card;
    }

    private function charge($data): Charge
    {
        $charge = new Charge();
        $this->mapper->mapObjectFromString(json_encode($data), $charge);
        return $charge;
    }

    private function customer($data): Customer
    {
        $customer = new Customer();
        $this->mapper->mapObjectFromString(json_encode($data), $customer);
        return $customer;
    }

    private function discount($data): Discount
    {
        $discount = new Discount();
        $this->mapper->mapObjectFromString(json_encode($data), $discount);
        return $discount;
    }

    private function invoice($data): Invoice
    {
        $invoice = new Invoice();
        $this->mapper->mapObjectFromString(json_encode($data), $invoice);
        return $invoice;
    }


    private function order($data): Order
    {
        $order = new Order();
        $this->mapper->mapObjectFromString(json_encode($data), $order);
        return $order;
    }


    private function plan($data): Plan
    {
        $plan = new Plan();
        $this->mapper->mapObjectFromString(json_encode($data), $plan);
        return $plan;
    }

    private function subscription($data): Subscription
    {
        $subscription = new Subscription();
        $this->mapper->mapObjectFromString(json_encode($data), $subscription);
        return $subscription;
    }
}
