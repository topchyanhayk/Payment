<?php

namespace App\Exceptions;

class SubscriptionDoesNotExistException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return BusinessLogicException::SUBSCRIPTION_DOES_NOT_EXIST;
    }

    public function getStatusMessage(): string
    {
        return __('errors.subscription_does_not_exist');
    }
}
