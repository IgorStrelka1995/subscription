<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class SubscriptionFactory extends Factory
{
    /**
     * @return array
     */
    public function definition(): array
    {
        $subscriptionPlan = SubscriptionPlan::all()->random();

        $planId = $subscriptionPlan->id;

        $starttime = $this->faker->dateTime;

        $endtime = match ($subscriptionPlan->name) {
            SubscriptionPlan::SUBSCRIPTION_LIGHT_PLAN => Carbon::instance($starttime)->addDays(SubscriptionPlan::SUBSCRIPTION_LIGHT_DURATION),
            SubscriptionPlan::SUBSCRIPTION_OPTIMAL_PLAN => Carbon::instance($starttime)->addDays(SubscriptionPlan::SUBSCRIPTION_OPTIMAL_DURATION),
            SubscriptionPlan::SUBSCRIPTION_MAXIMAL_PLAN => Carbon::instance($starttime)->addDays(SubscriptionPlan::SUBSCRIPTION_MAXIMAL_DURATION),
        };

        return [
            'user_id' => User::all()->random()->id,
            'plan_id' => $planId,
            'start_date' => $starttime,
            'end_date' => $endtime,
            'status' => $this->faker->randomElement(['active', 'cancelled', 'expired']),
        ];
    }
}
