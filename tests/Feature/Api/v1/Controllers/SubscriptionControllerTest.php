<?php

namespace Tests\Feature\Api\v1\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class SubscriptionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscribe_to_subscription_plan()
    {
        User::factory()->create();

        $this->assertDatabaseCount('users', 1);

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

        $this->assertDatabaseCount('subscription_plans', 3);

        $response = $this->postJson('/api/v1/subscription/subscribe', ["user_id" => "1", "plan_id" => "1"]);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                $json->hasAll(['id', 'user_id', 'plan_id', 'start_date', 'end_date', 'status', 'created_at'])
                    ->where('user_id', '1')
                    ->where('plan_id', '1')
                    ->where('start_date', Carbon::now()->toDateTimeString())
                    ->where('end_date', Carbon::now()->addDays(90)->toDateTimeString())
                    ->where('status', Subscription::SUBSCRIPTION_STATUS_ACTIVE)
                    ->etc();
            });
        });
    }

    public function test_cancel_subscribe()
    {
        User::factory()->create(['id' => 1]);

        $this->assertDatabaseCount('users', 1);

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

        $this->assertDatabaseCount('subscription_plans', 3);

        Subscription::factory()->create(['id' => 1, 'user_id' => 1, 'plan_id' => 1, 'status' => 'active']);

        $this->assertDatabaseCount('subscriptions', 1);

        $response = $this->putJson('api/v1/subscription/cancel/1');

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                $json->hasAll(['id', 'user_id', 'plan_id', 'start_date', 'end_date', 'status', 'created_at'])
                    ->where('user_id', 1)
                    ->where('plan_id', 1)
                    ->where('status', Subscription::SUBSCRIPTION_STATUS_CANCELLED)
                    ->etc();
            });
        });
    }

    public function test_prolongation_subscribe()
    {
        User::factory()->create(['id' => 1]);

        $this->assertDatabaseCount('users', 1);

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

        $this->assertDatabaseCount('subscription_plans', 3);

        $subscription = Subscription::factory()->create([
            'id' => 1, 'user_id' => 1, 'plan_id' => 1, 'status' => 'active',
            'start_date' => Carbon::create(2024, 6, 01)->toDateTimeString(),
            'end_date' => Carbon::create(2024, 6, 01)->addDays(90)->toDateTimeString()
        ]);

        $this->assertDatabaseCount('subscriptions', 1);

        $this->assertEquals("2024-06-01 00:00:00", $subscription->start_date);
        $this->assertEquals("2024-08-30 00:00:00", $subscription->end_date);

        $fixedDate = Carbon::create(2024, 7, 1);

        Carbon::setTestNow($fixedDate);

        $response = $this->putJson('api/v1/subscription/prolongation/1');

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                $json->hasAll(['id', 'user_id', 'plan_id', 'start_date', 'end_date', 'status', 'created_at'])
                    ->where('user_id', 1)
                    ->where('plan_id', 1)
                    ->where('start_date', '2024-06-01 00:00:00')
                    ->where('end_date', Carbon::now()->addDays(90)->toDateTimeString())
                    ->where('status', Subscription::SUBSCRIPTION_STATUS_ACTIVE)
                    ->etc();
            });
        });

        Carbon::setTestNow();
    }
}
