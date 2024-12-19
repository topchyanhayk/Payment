<?php

namespace App\Exceptions\Stripe;

use App\Exceptions\BusinessLogicException;

class StripeInvoiceDoesNotExistException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return BusinessLogicException::STRIPE_INVOICE_DOES_NOT_EXIST;
    }

    public function getStatusMessage(): string
    {
        return __('errors.stripe.invoice_does_not_exist');
    }
}
