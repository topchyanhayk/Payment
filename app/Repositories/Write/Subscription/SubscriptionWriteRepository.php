<?php

namespace App\Repositories\Write\Subscription;

use App\Exceptions\SavingErrorException;
use App\Models\Subscription\Subscription;
use App\Services\Dispatchers\EventDispatcherInterface;

class SubscriptionWriteRepository implements SubscriptionWriteRepositoryInterface
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function save(Subscription $subscription): bool
    {
        if (!$subscription->save()) {
            throw new SavingErrorException();
        }
        $this->dispatcher->dispatchAll($subscription->releaseEvents());
        return true;
    }
}
