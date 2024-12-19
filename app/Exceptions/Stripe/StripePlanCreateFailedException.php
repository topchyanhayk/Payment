<?php

namespace App\Exceptions\Stripe;

use App\Exceptions\BusinessLogicException;

class StripePlanCreateFailedException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return self::STRIPE_PLAN_CREATE_FAILED;
    }

    public function getStatusMessage(): string
    {
        return __('errors.stripe.create_plan_failed');
    }
}
