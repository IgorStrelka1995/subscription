<?php

namespace Tests\Feature\Controllers\Api\v1;

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

    public function test_list_of_subscription_of_user()
    {
        $user = User::factory()->create();

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

        Subscription::factory()->create(['id' => 1, 'user_id' => $user->id, 'plan_id' => 1, 'status' => 'active']);

        $response = $this->actingAs($user)->get('/api/v1/subscription');

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) use ($user) {
            $json->has('data.0', function (AssertableJson $json)  use ($user) {
                $json->hasAll(['id', 'plan', 'start_date', 'end_date', 'status', 'created_at'])
                    ->has('plan', function (AssertableJson $json) {
                        $json
                            ->hasAll(['id', 'name', 'description', 'price', 'duration', 'created_at', 'updated_at'])
                            ->where('name', SubscriptionPlan::SUBSCRIPTION_LIGHT_PLAN)
                        ;
                    })
                    ->where('status', Subscription::SUBSCRIPTION_STATUS_ACTIVE)
                    ->etc();
            });
        });
    }

    public function test_subscribe_to_subscription_plan()
    {
        $user = User::factory()->create();

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

        $response = $this->actingAs($user)->postJson('/api/v1/subscription/subscribe', ["user_id" => $user->id, "plan_id" => 1]);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) use ($user) {
            $json->has('data', function (AssertableJson $json)  use ($user) {
                $json->hasAll(['id', 'user_id', 'plan', 'start_date', 'end_date', 'status', 'created_at'])
                    ->where('user_id', $user->id)
                    ->has('plan', function (AssertableJson $json) {
                        $json
                            ->hasAll(['id', 'name', 'description', 'price', 'duration', 'created_at'])
                            ->where('name', SubscriptionPlan::SUBSCRIPTION_LIGHT_PLAN)
                        ;
                    })
                    ->where('start_date', Carbon::now()->toDateTimeString())
                    ->where('end_date', Carbon::now()->addDays(90)->toDateTimeString())
                    ->where('status', Subscription::SUBSCRIPTION_STATUS_ACTIVE)
                    ->etc();
            });
        });
    }

    public function test_cancel_subscribe()
    {
        $user = User::factory()->create(['id' => 1]);

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

        Subscription::factory()->create(['id' => 1, 'user_id' => 1, 'plan_id' => 1, 'status' => 'active']);

        $this->assertDatabaseCount('subscriptions', 1);

        $response = $this->actingAs($user)->putJson('api/v1/subscription/cancel/1');

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json
                ->has('data', function (AssertableJson $json) {
                $json
                    ->hasAll(['id', 'user_id', 'plan', 'start_date', 'end_date', 'status', 'created_at'])
                    ->where('user_id', 1)
                    ->has('plan', function (AssertableJson $json) {
                        $json
                            ->hasAll(['id', 'name', 'description', 'price', 'duration', 'created_at'])
                            ->where('name', SubscriptionPlan::SUBSCRIPTION_LIGHT_PLAN)
                        ;
                    })
                    ->where('status', Subscription::SUBSCRIPTION_STATUS_CANCELLED)
                    ->etc();
            });
        });
    }

    public function test_cancel_subscribe_another_user()
    {
        User::factory()->create(['id' => 1]);

        $user2 = User::factory()->create(['id' => 2]);

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

        Subscription::factory()->create(['id' => 1, 'user_id' => 1, 'plan_id' => 1, 'status' => 'active']);

        $this->assertDatabaseCount('subscriptions', 1);

        $response = $this->actingAs($user2)->putJson('api/v1/subscription/cancel/1');

        $response->assertStatus(403);
    }

    public function test_prolongation_subscribe()
    {
        $user = User::factory()->create(['id' => 1]);

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

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

        $response = $this->actingAs($user)->putJson('api/v1/subscription/prolongation/1');

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                $json->hasAll(['id', 'user_id', 'plan', 'start_date', 'end_date', 'status', 'created_at'])
                    ->where('user_id', 1)
                    ->has('plan', function (AssertableJson $json) {
                        $json
                            ->hasAll(['id', 'name', 'description', 'price', 'duration', 'created_at'])
                            ->where('name', SubscriptionPlan::SUBSCRIPTION_LIGHT_PLAN)
                        ;
                    })
                    ->where('start_date', '2024-06-01 00:00:00')
                    ->where('end_date', Carbon::now()->addDays(90)->toDateTimeString())
                    ->where('status', Subscription::SUBSCRIPTION_STATUS_ACTIVE)
                    ->etc();
            });
        });

        Carbon::setTestNow();
    }

    public function test_prolongation_subscribe_for_another_user()
    {
        User::factory()->create(['id' => 1]);

        $user2 = User::factory()->create(['id' => 2]);

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

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

        $response = $this->actingAs($user2)->putJson('api/v1/subscription/prolongation/1');

        $response->assertStatus(403);

        Carbon::setTestNow();
    }
}
