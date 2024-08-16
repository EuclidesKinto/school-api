<?php

namespace App\Services\Stripe;

use Stripe\StripeClient;

class StripeService
{

    private $stripe;

    public function __construct($secret_key)
    {
        $this->stripe = new StripeClient(
            $secret_key
        );
    }

    /**
     * Returns the stripe client object
     */
    public function stripe()
    {
        return $this->stripe;
    }

    /**
     * Manage Stripe Subscriptions
     */
    public function subscriptions()
    {
        return $this->stripe->subscriptions;
    }

    /**
     * Manage Stripe Customers
     */
    public function customers()
    {
        return $this->stripe->customers;
    }

    /**
     * Manage Stripe Plans
     */
    public function plans()
    {
        return $this->stripe->plans;
    }

    /**
     * Manage Stripe Invoices
     */
    public function invoices()
    {
        return $this->stripe->invoices;
    }

    /**
     * Manage Stripe Charges
     */
    public function charges()
    {
        return $this->stripe->charges;
    }

    /**
     * Manage Stripe transactions
     */
    public function transactions()
    {
        return $this->stripe->balanceTransactions;
    }

    /**
     * Manage Stripe Billing Portal
     */
    public function billingPortal()
    {
        return $this->stripe->billingPortal;
    }
}
