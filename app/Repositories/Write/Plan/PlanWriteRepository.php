<?php

namespace App\Repositories\Write\Plan;

use App\Exceptions\SavingErrorException;
use App\Models\Plan\Plan;
use App\Models\Plan\PlanPlatform;

class PlanWriteRepository implements PlanWriteRepositoryInterface
{
    public function save(Plan $plan): bool
    {
        if (!$plan->save()) {
            throw new SavingErrorException();
        }
        return true;
    }

    public function savePlanPlatform(PlanPlatform $planPlatform): bool
    {
        if (!$planPlatform->save()) {
            throw new SavingErrorException();
        }
        return true;
    }
}
