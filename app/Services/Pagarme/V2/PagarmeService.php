<?php

namespace App\Services\Pagarme\V2;

use App\Exceptions\Orders\DeleteCardException;
use App\Exceptions\Pagarme\FailedToCreateOrderException;
use App\Exceptions\Subscriptions\FailedToCreateRecurrenceException;
use App\Exceptions\Subscriptions\SubscriptionNotFoundException;
use App\Services\Webhook\Pagarme\Support\Models\Card as PagarmeCard;
use App\Services\Webhook\Pagarme\Support\Models\Order as PagarmeOrder;
use App\Services\Webhook\Pagarme\Support\Models\Subscription as PagarmeSubscription;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use JsonMapper\JsonMapperFactory;

class PagarmeService
{

    private $http;
    private $mapper;

    public function __construct()
    {
        $this->http = new Client([
            'base_uri' => config('pagarme.base_api'),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode(config('pagarme.secret_key') . ':'),
                'User-Agent' => 'PagarmeCoreApi - PHP 5.5.0',
            ]
        ]);

        $this->mapper = (new JsonMapperFactory())->bestFit();
    }


    public function get($uri, $options = [])
    {
        return $this->http->request('GET', $uri, $options);
    }

    public function post($uri, $options = [])
    {
        return $this->http->request('POST', $uri, $options);
    }

    public function put($uri, $options = [])
    {
        return $this->http->request('PUT', $uri, $options);
    }

    public function delete($uri, $options = [])
    {
        return $this->http->request('DELETE', $uri, $options);
    }

    public function getCharges($query)
    {

        $response = $this->get('charges', [
            'query' => $query
        ]);

        $charges = json_decode($response->getBody());

        return collect($charges->data);
    }


    public function getInvoices($query)
    {

        $response = $this->get('invoices', [
            'query' => $query
        ]);

        $invoices = json_decode($response->getBody());

        return collect($invoices->data);
    }

    public function getInvoice($invoice_id)
    {
        $response = $this->get("invoices/{$invoice_id}", []);
        $invoice = json_decode($response->getBody());
        return $invoice;
    }


    public function subscribe($subscription)
    {
        try {
            $response = $this->post('subscriptions', [
                'json' => $subscription
            ]);
            $pagarme_subscription = new PagarmeSubscription();
            $this->mapper->mapObjectFromString($response->getBody(), $pagarme_subscription);
            return $pagarme_subscription;
        } catch (ClientException $e) {
            throw new FailedToCreateRecurrenceException($e->getMessage(), $e->getCode());
        }
    }

    public function unsubscribe($subscription_id, $cancel_pending_invoices = false)
    {
        try {
            $response = $this->delete("subscriptions/$subscription_id", [
                'json' => ['cancel_pending_invoices' => $cancel_pending_invoices]
            ]);
            $subscription = new PagarmeSubscription();
            $this->mapper->mapObjectFromString($response->getBody(), $subscription);
            return $subscription;
        } catch (ClientException $e) {
            throw new SubscriptionNotFoundException("Esta subscription não existe ou já foi cancelada.", 404, $e);
        }
    }

    public function getSubscription($subscription_id)
    {
        try {
            $response = $this->get("subscriptions/$subscription_id", []);
            $subscription = new PagarmeSubscription();
            $this->mapper->mapObjectFromString($response->getBody(), $subscription);
            return $subscription;
        } catch (ClientException $e) {
            throw new SubscriptionNotFoundException("Esta subscription não existe ou já foi cancelada.", 404, $e);
        }
    }

    public function subscribeByOrder($order)
    {
        try {
            $response = $this->post('orders', [
                'json' => $order
            ]);
            $order = new PagarmeOrder();
            $this->mapper->mapObjectFromString($response->getBody(), $order);
            return $order;
        } catch (ClientException $e) {
            throw new FailedToCreateOrderException($e->getMessage(), $e->getCode());
        }
    }

    public function createCard($card)
    {
        //
    }

    public function deleteCard($customer_id, $card_id)
    {
        try {
            $response = $this->delete("customers/$customer_id/cards/$card_id", []);
            $card = new PagarmeCard();
            $this->mapper->mapObjectFromString($response->getBody(), $card);
            return $card;
        } catch (ClientException $e) {
            throw new DeleteCardException($e->getMessage(), $e->getCode());
        }
    }
}
