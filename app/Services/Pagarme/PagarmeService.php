<?php

namespace App\Services\Pagarme;

use PagarmeCoreApiLib\PagarmeCoreApiClient;

class PagarmeService
{

    protected $client;

    /**
     * Pagarme Service Constructor 
     * 
     * @param string $basicAuthUserName
     * @param string $basicAuthPassword
     * @return \PagarmeCoreApiLib\PagarmeCoreApiClient
     */
    public function __construct($basicAuthUserName = null, $basicAuthPassword = null)
    {
        $this->client = new PagarmeCoreApiClient($basicAuthUserName, $basicAuthPassword);
    }

    /**
     * Get plans controller
     */
    public function plans()
    {
        return $this->client->getPlans();
    }

    /**
     * Get subscriptions controller
     */
    public function subscriptions()
    {
        return $this->client->getSubscriptions();
    }

    /**
     * Get invoices controller
     * 
     */
    public function invoices()
    {
        return $this->client->getInvoices();
    }

    /**
     * Get Orders controller
     * 
     */
    public function orders()
    {
        return $this->client->getOrders();
    }

    /**
     * Get customers controller
     * 
     */
    public function customers()
    {
        return $this->client->getCustomers();
    }

    /**
     * Get recipients controller
     * 
     */
    public function recipients()
    {
        return $this->client->getRecipients();
    }

    /**
     * Get charges controller
     * 
     */
    public function charges()
    {
        return $this->client->getCharges();
    }

    /**
     * Get transfers controller
     * 
     */
    public function transfers()
    {
        return $this->client->getTransfers();
    }

    /**
     * Get tokens controller
     * 
     */
    public function tokens()
    {
        return $this->client->getTokens();
    }

    /**
     * Get transactions controller
     * 
     */
    public function transactions()
    {
        return $this->client->getTransactions();
    }
}
