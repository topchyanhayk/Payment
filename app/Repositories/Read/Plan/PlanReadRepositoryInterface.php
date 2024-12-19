<?php

namespace App\Repositories\Read\Plan;

use App\Models\Plan\Plan;
use App\Models\Plan\PlanPlatform;

interface PlanReadRepositoryInterface
{
    public function getPlanPlatform(string $planId, string $platform): ?PlanPlatform;
    public function getById(string $planId): ?Plan;
    public function getPlatform(Plan $plan, string $platformType): PlanPlatform;
    public function getByPlatformId(string $planPlatformId): PlanPlatform;
}
