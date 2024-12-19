<?php

namespace App\Events\Subscription;

use App\Models\Subscription\Subscription;
use Illuminate\Foundation\Events\Dispatchable;

class SubscriptionPlanChangeFailedEvent
{
    use Dispatchable;

    public Subscription $subscription;

    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }
}
