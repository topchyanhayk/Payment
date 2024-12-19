<?php

namespace App\Repositories\Read\Subscription;

use App\Exceptions\SubscriptionDoesNotExistException;
use App\Models\Subscription\Subscription;

class SubscriptionReadRepository implements SubscriptionReadRepositoryInterface
{
    public function getById(string $subscriptionId): ?Subscription
    {
        $subscription = Subscription::find($subscriptionId);
        if (is_null($subscription)) {
            throw new SubscriptionDoesNotExistException();
        }

        return $subscription;
    }

    public function getBySession(string $sessionId): ?Subscription
    {
        $subscription = Subscription::where('platform_session_id', $sessionId)
            ->first();

        if (is_null($subscription)) {
            throw new SubscriptionDoesNotExistException();
        }

        return $subscription;
    }

    public function getByPlatformId(string $platformSubscriptionId): ?Subscription
    {
        $subscription = Subscription::where('platform_subscription_id', $platformSubscriptionId)
            ->first();

        if (is_null($subscription)) {
            throw new SubscriptionDoesNotExistException();
        }

        return $subscription;
    }
}
