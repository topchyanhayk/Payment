<?php

namespace App\Providers;

use App\Models\Plan\Plan;
use App\Models\Subscription\Subscription;
use App\Policies\Plan\PlanPolicy;
use App\Policies\Subscription\SubscriptionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Subscription::class => SubscriptionPolicy::class,
        Plan::class => PlanPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
