<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SubscriptionPlan::factory()->create([
            'name' => SubscriptionPlan::SUBSCRIPTION_LIGHT_PLAN,
            'price' => SubscriptionPlan::SUBSCRIPTION_LIGHT_PRICE,
            'duration' => SubscriptionPlan::SUBSCRIPTION_LIGHT_DURATION
        ]);

        SubscriptionPlan::factory()->create([
            'name' => SubscriptionPlan::SUBSCRIPTION_OPTIMAL_PLAN,
            'price' => SubscriptionPlan::SUBSCRIPTION_OPTIMAL_PRICE,
            'duration' => SubscriptionPlan::SUBSCRIPTION_OPTIMAL_DURATION
        ]);

        SubscriptionPlan::factory()->create([
            'name' => SubscriptionPlan::SUBSCRIPTION_MAXIMAL_PLAN,
            'price' => SubscriptionPlan::SUBSCRIPTION_MAXIMAL_PRICE,
            'duration' => SubscriptionPlan::SUBSCRIPTION_MAXIMAL_DURATION
        ]);
    }
}
