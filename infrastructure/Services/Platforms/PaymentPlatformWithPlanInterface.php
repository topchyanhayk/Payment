<?php

namespace Infrastructure\Services\Platforms;

use App\Services\Plan\Dto\CreatePlanDto;
use App\Services\Plan\Dto\PlanEntityDto;

interface PaymentPlatformWithPlanInterface extends PaymentPlatformInterface
{
    public function publishPlan(CreatePlanDto $dto): PlanEntityDto;
}
