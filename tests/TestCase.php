<?php

namespace Tests;

use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @var array|array[]
     */
    protected static array $subscriptionPlans = [
        [
            'id' => 1,
            'name' => SubscriptionPlan::SUBSCRIPTION_LIGHT_PLAN,
            'price' => SubscriptionPlan::SUBSCRIPTION_LIGHT_PRICE,
            'duration' => SubscriptionPlan::SUBSCRIPTION_LIGHT_DURATION,
            'description' => "Light subscription plan."
        ],
        [
            'id' => 2,
            'name' => SubscriptionPlan::SUBSCRIPTION_OPTIMAL_PLAN,
            'price' => SubscriptionPlan::SUBSCRIPTION_OPTIMAL_PRICE,
            'duration' => SubscriptionPlan::SUBSCRIPTION_OPTIMAL_DURATION,
            'description' => "Optimal subscription plan."
        ],
        [
            'id' => 3,
            'name' => SubscriptionPlan::SUBSCRIPTION_MAXIMAL_PLAN,
            'price' => SubscriptionPlan::SUBSCRIPTION_MAXIMAL_PRICE,
            'duration' => SubscriptionPlan::SUBSCRIPTION_MAXIMAL_DURATION,
            'description' => "Maximal subscription plan."
        ]
    ];
}
