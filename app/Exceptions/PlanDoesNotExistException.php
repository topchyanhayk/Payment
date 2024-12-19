<?php

namespace App\Exceptions;

class PlanDoesNotExistException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return BusinessLogicException::PLAN_DOES_NOT_EXIST;
    }

    public function getStatusMessage(): string
    {
        return __('errors.plan_does_not_exist');
    }
}
