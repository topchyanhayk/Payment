<?php

namespace App\Repositories\Read\Plan;

use App\Exceptions\PlanDoesNotExistException;
use App\Exceptions\PlanPlatformDoesNotExistException;
use App\Models\Plan\Plan;
use App\Models\Plan\PlanPlatform;

class PlanReadRepository implements PlanReadRepositoryInterface
{
    public function getPlanPlatform(string $planId, string $platform): ?PlanPlatform
    {
        $planPlatform = PlanPlatform::where('plan_id', $planId)
            ->where('platform_type', $platform)
            ->first();

        if (is_null($planPlatform)) {
            throw new PlanPlatformDoesNotExistException();
        }

        return $planPlatform;
    }

    public function getById(string $planId): ?Plan
    {
        $plan = Plan::find($planId);
        if (is_null($plan)) {
            throw new PlanDoesNotExistException();
        }

        return $plan;
    }

    public function getPlatform(Plan $plan, string $platformType): PlanPlatform
    {
        return $plan->platforms
            ->where('platform_type', $platformType)
            ->first();
    }

    public function getByPlatformId(string $planPlatformId): PlanPlatform
    {
        $planPlatform = PlanPlatform::where('platform_plan_id', $planPlatformId)
            ->first();

        if (is_null($planPlatform)) {
            throw new PlanPlatformDoesNotExistException();
        }

        return $planPlatform;
    }
}
