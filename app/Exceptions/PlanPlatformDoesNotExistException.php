<?php

namespace App\Exceptions;

class PlanPlatformDoesNotExistException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return BusinessLogicException::PLAN_PLATFORM_DOES_NOT_EXIST;
    }

    public function getStatusMessage(): string
    {
        return __('errors.plan_platform_does_not_exist');
    }
}
