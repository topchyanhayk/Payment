<?php

namespace App\Repositories\Write\Plan;

use App\Models\Plan\Plan;
use App\Models\Plan\PlanPlatform;

interface PlanWriteRepositoryInterface
{
    public function save(Plan $plan): bool;
    public function savePlanPlatform(PlanPlatform $planPlatform): bool;
}
