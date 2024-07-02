<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;

class SubscriptionService
{
    /**
     * Generate start_date, end_date and status for Subscription
     *
     * @param array $subscription
     * @return array
     */
    public function generateSubscriptionData(array $subscription)
    {
        $subscriptionPlan = SubscriptionPlan::find($subscription['plan_id']);

        $carbon = Carbon::now();

        $starttime = $carbon->toDateTimeString();

        $endtime = $carbon->addDays($subscriptionPlan->duration)->toDateTimeString();

        $subscription['start_date'] = $starttime;

        $subscription['end_date'] = $endtime;

        $subscription['status'] = Subscription::SUBSCRIPTION_STATUS_ACTIVE;

        return $subscription;
    }

    /**
     * @param Subscription $subscription
     * @return array
     */
    public function prolongationSubscription(Subscription $subscription)
    {
        $subscriptionPlan = SubscriptionPlan::find($subscription->plan_id);

        $carbon = Carbon::now();

        $end_date = $carbon->addDays($subscriptionPlan->duration)->toDateTimeString();

        return ['end_date' => $end_date];
    }
}
