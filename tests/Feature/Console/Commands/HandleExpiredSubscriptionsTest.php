<?php

namespace Tests\Feature\Console\Commands;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HandleExpiredSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_change_status_to_expired_for_subscription(): void
    {
        User::factory()->create(['id' => 1]);

        $this->assertDatabaseCount('users', 1);

        SubscriptionPlan::factory()->createMany(self::$subscriptionPlans);

        $this->assertDatabaseCount('subscription_plans', 3);

        $start_date = Carbon::create(2023, 1, 1)->toDateTimeString();
        $end_date = Carbon::create(2024, 1, 1)->toDateTimeString();

        Subscription::factory()->create([
            'id' => 1, 'user_id' => 1, 'plan_id' => 1, 'status' => 'active',
            'start_date' => $start_date, 'end_date' => $end_date
        ]);

        $this->assertDatabaseCount('subscriptions', 1);

        $this->assertDatabaseHas('subscriptions', ['status' => Subscription::SUBSCRIPTION_STATUS_ACTIVE]);

        $fixedDate = Carbon::create(2024, 2, 1);

        Carbon::setTestNow($fixedDate);

        $this->artisan('subscriptions:handle')->assertExitCode(0);

        $this->assertDatabaseHas('subscriptions', ['status' => Subscription::SUBSCRIPTION_STATUS_EXPIRED]);

        Carbon::setTestNow();
    }
}
