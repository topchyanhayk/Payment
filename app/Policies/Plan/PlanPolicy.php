<?php

namespace App\Policies\Plan;

use App\Models\Client\Client;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlanPolicy
{
    use HandlesAuthorization;

    public function create(Client $byClient, Client $forClient): bool
    {
        return $byClient->getId()->getValue() === $forClient->getId()->getValue();
    }
}
