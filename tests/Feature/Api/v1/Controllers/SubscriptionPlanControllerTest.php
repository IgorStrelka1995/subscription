<?php

namespace Tests\Feature\Api\v1\Controllers;

use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class SubscriptionPlanControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_receive_subscription_plans()
    {
        // Only admin could create this actions. Should finish tests

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

        $this->assertDatabaseCount('subscription_plans', 3);

        $response = $this->get('/api/v1/subscription_plan');

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json)  {
            $json->hasAll(['data'])
                ->has('data', 3)
                ->has('data.0',function (AssertableJson $json) {
                    $json->hasAll(['id', 'name', 'description', 'price', 'duration', 'created_at']);
                    $json->where('name', SubscriptionPlan::SUBSCRIPTION_LIGHT_PLAN);
                })->has('data.1',function (AssertableJson $json) {
                    $json->hasAll(['id', 'name', 'description', 'price', 'duration', 'created_at']);
                    $json->where('name', SubscriptionPlan::SUBSCRIPTION_OPTIMAL_PLAN);
                })->has('data.2',function (AssertableJson $json) {
                    $json->hasAll(['id', 'name', 'description', 'price', 'duration', 'created_at']);
                    $json->where('name', SubscriptionPlan::SUBSCRIPTION_MAXIMAL_PLAN);
                })
            ;
        });
    }

    public function test_receive_subscription_plans_with_filter()
    {
        // Only admin could create this actions. Should finish tests

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

        $this->assertDatabaseCount('subscription_plans', 3);

        $response = $this->get('/api/v1/subscription_plan?filter[name]=Light');

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json)  {
            $json->hasAll(['data'])
                ->has('data', 1)
                ->has('data.0',function (AssertableJson $json) {
                    $json->hasAll(['id', 'name', 'description', 'price', 'duration', 'created_at']);
                    $json->where('name', SubscriptionPlan::SUBSCRIPTION_LIGHT_PLAN);
                })
            ;
        });

        $response = $this->get('/api/v1/subscription_plan?filter[price]=500');

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json)  {
            $json->hasAll(['data'])
                ->has('data', 1)
                ->has('data.0',function (AssertableJson $json) {
                    $json->hasAll(['id', 'name', 'description', 'price', 'duration', 'created_at']);
                    $json->where('name', SubscriptionPlan::SUBSCRIPTION_OPTIMAL_PLAN);
                })
            ;
        });

        $response = $this->get('/api/v1/subscription_plan?filter[description]=Maximal subscription');

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json)  {
            $json->hasAll(['data'])
                ->has('data', 1)
                ->has('data.0',function (AssertableJson $json) {
                    $json->hasAll(['id', 'name', 'description', 'price', 'duration', 'created_at']);
                    $json->where('name', SubscriptionPlan::SUBSCRIPTION_MAXIMAL_PLAN);
                })
            ;
        });
    }

    public function test_receive_single_subscription_plan()
    {
        // Only admin could create this actions. Should finish tests

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

        $this->assertDatabaseCount('subscription_plans', 3);

        $this->assertDatabaseHas('subscription_plans', ['id' => 1]);

        $response = $this->get('/api/v1/subscription_plan/1');

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                $json->hasAll(['id', 'name', 'description', 'price', 'duration', 'created_at']);
                $json->where('name', SubscriptionPlan::SUBSCRIPTION_LIGHT_PLAN);
            });
        });
    }

    public function test_store_subscription_plan()
    {
        // Only admin could create this actions. Should finish tests

        $response = $this->postJson('/api/v1/subscription_plan', [
            "name" => "Maximal+",
            "description" => "Voluptatum sequi odio sint dolorem consectetur nihil quasi.",
            "price" => "200.00",
            "duration" => 545
        ]);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                $json->hasAll(['id', 'name', 'description', 'price', 'duration', 'created_at'])
                    ->where('name', 'Maximal+')
                    ->where('description', 'Voluptatum sequi odio sint dolorem consectetur nihil quasi.')
                    ->where('price', "200.00")
                    ->where('duration', 545)
                    ->etc();
            });
        });
    }

    public function test_update_subscription_plan()
    {
        // Only admin could create this actions. Should finish tests

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

        $this->assertDatabaseCount('subscription_plans', 3);

        $this->assertDatabaseHas('subscription_plans', ['name' => SubscriptionPlan::SUBSCRIPTION_LIGHT_PLAN]);

        $response = $this->putJson("/api/v1/subscription_plan/1", ["name" => "Light+"]);

        $response->assertStatus(200);

        $response->assertJson(function (AssertableJson $json) {
            $json->has('data', function (AssertableJson $json) {
                $json
                    ->hasAll(['id', 'name', 'description', 'price', 'duration', 'created_at'])
                    ->where('name', 'Light+')->etc()
                ;
            });
        });
    }

    public function test_destroy_subscription_plan()
    {
        // Only admin could create this actions. Should finish tests

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

        $this->assertDatabaseCount('subscription_plans', 3);

        $this->assertDatabaseHas('subscription_plans', ['name' => SubscriptionPlan::SUBSCRIPTION_LIGHT_PLAN]);

        $response = $this->delete('/api/v1/subscription_plan/1');

        $response->assertStatus(204);

        $this->assertDatabaseCount('subscription_plans', 2);
    }
}
