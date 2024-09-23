<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\SubscriptionPlan;
use App\Policies\SubscriptionPlanPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        SubscriptionPlan::class => SubscriptionPlanPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
