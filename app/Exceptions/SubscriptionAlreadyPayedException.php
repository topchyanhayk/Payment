<?php

namespace App\Exceptions;

class SubscriptionAlreadyPayedException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return BusinessLogicException::SUBSCRIPTION_ALREADY_PAYED;
    }

    public function getStatusMessage(): string
    {
        return __('errors.subscription_already_payed');
    }
}
