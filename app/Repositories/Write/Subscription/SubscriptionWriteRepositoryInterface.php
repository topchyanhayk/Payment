<?php

namespace App\Repositories\Write\Subscription;

use App\Models\Subscription\Subscription;

interface SubscriptionWriteRepositoryInterface
{
    public function save(Subscription $subscription): bool;
}
