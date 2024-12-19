<?php

namespace App\Services\Factories;

use App\Exceptions\PlatformDoesNotExistException;
use Infrastructure\Services\Platforms\Stripe\StripeService;
use Infrastructure\Services\Platforms\PaymentPlatformInterface;
use Stripe\StripeClient;

class PaymentPlatformFactory
{
    const STRIPE = 'stripe';

    const CURRENCY_EUR = 'EUR';
    const CURRENCY_USD = 'USD';
    const CURRENCY_RUB = 'RUB';

    const INTERVAL_DAY   = 'day';
    const INTERVAL_WEEK  = 'week';
    const INTERVAL_MONTH = 'month';
    const INTERVAL_YEAR  = 'year';

    public function getPaymentPlatformClient($platform): PaymentPlatformInterface
    {
        switch ($platform) {
            case self::STRIPE:
                return new StripeService(app()->make(StripeClient::class));
            default:
                throw new PlatformDoesNotExistException();
        }
    }
}
