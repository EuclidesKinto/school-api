<?php

namespace App\Providers;

use App\Models\ChallengeOwn;
use App\Observers\ChallengeOwnObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\FlagPowned;
use App\Listeners\UpdateScoreboard;
use App\Models\Address;
use App\Models\BillingProfile;
use App\Models\Discount;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Plan;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Webhook;
use App\Observers\BillingProfileObserver;
use App\Observers\OrderItemObserver;
use App\Observers\OrderObserver;
use App\Observers\PlanObserver;
use App\Observers\ProductObserver;
use App\Observers\UserObserver;
use App\Observers\AddressObserver;
use App\Observers\DiscountObserver;
use App\Observers\SubscriptionObserver;
use App\Observers\WebhookObserver;
use App\Observers\InvoiceObserver;
use App\Models\Comment;
use App\Models\Hacktivity;
use App\Observers\CommentObserver;
use App\Observers\HacktivityObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\LogSuccessfulLogin',
        ],

        // FlagPowned::class => [
        //     UpdateScoreboard::class
        // ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Observe for events regarding the User model
         */
        User::observe(UserObserver::class);

        /**
         * Observe for events regarding the Plan model
         */
        Plan::observe(PlanObserver::class);

        /**
         * Observe for events regarding the Product model
         */
        Product::observe(ProductObserver::class);

        /**
         * Observe for order events
         */
        Order::observe(OrderObserver::class);

        /**
         * Observe for OrderItem's model events
         */
        OrderItem::observe(OrderItemObserver::class);

        /**
         * Observer for BillingProfile's model events
         */
        BillingProfile::observe(BillingProfileObserver::class);

        /**
         * Observe for events regarding the Address model
         */
        Address::observe(AddressObserver::class);

        /**
         * Observe events regarding the Discount model
         */
        Discount::observe(DiscountObserver::class);

        /**
         * Observer for events regarding the Webhook model
         */
        Webhook::observe(WebhookObserver::class);

        /**
         * Observe for events regarding the Subscription model
         */
        Subscription::observe(SubscriptionObserver::class);

        /**
         * Observe for events regarding the Invoice model
         */
        Invoice::observe(InvoiceObserver::class);

        /**
         * Observe for events regarding the Comment and Hacktivity models
         */

        Comment::observe(CommentObserver::class);
        Hacktivity::observe(HacktivityObserver::class);
        ChallengeOwn::observe(ChallengeOwnObserver::class);
    }
}
