<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $guarded = [];

    const SUBSCRIPTION_LIGHT_PLAN = 'Light';
    const SUBSCRIPTION_OPTIMAL_PLAN = 'Optimal';
    const SUBSCRIPTION_MAXIMAL_PLAN = 'Maximal';

    const SUBSCRIPTION_LIGHT_DURATION = 90;
    const SUBSCRIPTION_OPTIMAL_DURATION = 180;
    const SUBSCRIPTION_MAXIMAL_DURATION = 365;

    const SUBSCRIPTION_LIGHT_PRICE = 100;
    const SUBSCRIPTION_OPTIMAL_PRICE = 500;
    const SUBSCRIPTION_MAXIMAL_PRICE = 1000;

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
