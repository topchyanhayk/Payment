<?php

namespace App\Exceptions\Stripe;

use App\Exceptions\BusinessLogicException;

class StripeSessionDoesNotExistException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return BusinessLogicException::STRIPE_SESSION_DOES_NOT_EXIST;
    }

    public function getStatusMessage(): string
    {
        return __('errors.stripe.session_does_not_exist');
    }
}
