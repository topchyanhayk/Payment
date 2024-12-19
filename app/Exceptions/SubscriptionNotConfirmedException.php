<?php

namespace App\Exceptions;

class SubscriptionNotConfirmedException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return BusinessLogicException::SUBSCRIPTION_NOT_CONFIRMED;
    }

    public function getStatusMessage(): string
    {
        return __('errors.subscription_not_confirmed');
    }
}
