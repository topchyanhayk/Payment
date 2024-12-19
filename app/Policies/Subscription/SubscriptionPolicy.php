<?php

namespace App\Policies\Subscription;

use App\Models\Client\Client;
use App\Models\Subscription\Subscription;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionPolicy
{
    use HandlesAuthorization;

    public function create(Client $byClient, Client $forClient): bool
    {
        return $byClient->getId()->getValue() === $forClient->getId()->getValue();
    }

    public function pay(Client $client, Subscription $subscription): bool
    {
        return $client->getId()->getValue() === $subscription->getPlan()->getClientId();
    }

    public function get(Client $client, Subscription $subscription): bool
    {
        return $client->getId()->getValue() === $subscription->getPlan()->getClientId();
    }

    public function cancel(Client $client, Subscription $subscription): bool
    {
        return $client->getId()->getValue() === $subscription->getPlan()->getClientId();
    }

    public function update(Client $client, Subscription $subscription): bool
    {
        return $client->getId()->getValue() === $subscription->getPlan()->getClientId();
    }
}
