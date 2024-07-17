<?php

namespace Tests\Feature\Controllers\Api\v1;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function receive_payments()
    {

    }

    public function test_make_payment_with_stripe()
    {
        $user = User::factory()->create();

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

        $subscription = Subscription::factory()->create(['plan_id' => 1]);

        $response = $this->postJson('/api/v1/payment/stripe', [
            "user_id" => $user->id, "subscription_id" => $subscription->id
        ]);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) use ($user, $subscription) {
            $json->has('data', function (AssertableJson $json)  use ($user, $subscription) {
                $json->hasAll(['id', 'user_id', 'subscription_id', 'amount', 'payment_method', 'payment_status', 'created_at'])
                    ->where('user_id', $user->id)
                    ->where('subscription_id', $subscription->id)
                    ->where('amount', '100.00')
                    ->where('payment_method', Payment::STRIPE_PAYMENT_METHOD)
                    ->where('payment_status', Payment::PENDING_PAYMENT_METHOD)
                    ->etc();
            });
        });
    }

    public function test_make_payment_with_paypal()
    {
        $user = User::factory()->create();

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

        $subscription = Subscription::factory()->create(['plan_id' => 1]);

        $response = $this->postJson('/api/v1/payment/paypal', [
            "user_id" => $user->id, "subscription_id" => $subscription->id
        ]);

        $response->assertStatus(201);

        $response->assertJson(function (AssertableJson $json) use ($user, $subscription) {
            $json->has('data', function (AssertableJson $json)  use ($user, $subscription) {
                $json->hasAll(['id', 'user_id', 'subscription_id', 'amount', 'payment_method', 'payment_status', 'created_at'])
                    ->where('user_id', $user->id)
                    ->where('subscription_id', $subscription->id)
                    ->where('amount', '100.00')
                    ->where('payment_method', Payment::PAYPAL_PAYMENT_METHOD)
                    ->where('payment_status', Payment::PENDING_PAYMENT_METHOD)
                    ->etc();
            });
        });
    }
}
