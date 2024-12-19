<?php

namespace App\Repositories\Read\Subscription;

use App\Models\Subscription\Subscription;

interface SubscriptionReadRepositoryInterface
{
    public function getById(string $subscriptionId): ?Subscription;
    public function getBySession(string $sessionId): ?Subscription;
    public function getByPlatformId(string $platformSubscriptionId): ?Subscription;
}
