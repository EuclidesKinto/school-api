<?php

declare(strict_types=1);

return [

    // Manage autoload migrations
    'autoload_migrations' => false,

    // Subscriptions Database Tables
    'tables' => [

        'plans' => 'plans',
        'plan_features' => 'plan_features',
        'plan_subscriptions' => 'subscriptions',
        'plan_subscription_usage' => 'plan_subscription_usage',

    ],

    // Subscriptions Models
    'models' => [

        'plan' => \App\Models\Plan::class,
        'plan_feature' => \Rinvex\Subscriptions\Models\PlanFeature::class,
        'plan_subscription' => \App\Models\Subscription::class,
        'plan_subscription_usage' => \Rinvex\Subscriptions\Models\PlanSubscriptionUsage::class,

    ],

];
