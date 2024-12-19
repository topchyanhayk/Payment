<?php

use App\Services\Factories\PaymentPlatformFactory;
use Infrastructure\Services\Platforms\Stripe\StripeService;

return [
    'platforms' => [
        PaymentPlatformFactory::STRIPE,
    ],

    'stripe' => [
        'publicKey'  => env('STRIPE_PUBLIC_KEY'),
        'secretKey'  => env('STRIPE_SECRET_KEY'),
        'productId'  => env('STRIPE_PRODUCT_ID'),

        'currency'   => [
            PaymentPlatformFactory::CURRENCY_EUR => StripeService::CURRENCY_EUR,
            PaymentPlatformFactory::CURRENCY_USD => StripeService::CURRENCY_USD,
            PaymentPlatformFactory::CURRENCY_RUB => StripeService::CURRENCY_RUB,
        ],

        'interval'    => [
            PaymentPlatformFactory::INTERVAL_DAY   => StripeService::INTERVAL_DAY,
            PaymentPlatformFactory::INTERVAL_WEEK  => StripeService::INTERVAL_WEEK,
            PaymentPlatformFactory::INTERVAL_MONTH => StripeService::INTERVAL_MONTH,
            PaymentPlatformFactory::INTERVAL_YEAR  => StripeService::INTERVAL_YEAR,
        ]
    ],

    'available_currencies' => [
        PaymentPlatformFactory::CURRENCY_EUR,
        PaymentPlatformFactory::CURRENCY_USD,
        PaymentPlatformFactory::CURRENCY_RUB,
    ],

    'available_subscription_intervals' => [
        PaymentPlatformFactory::INTERVAL_DAY,
        PaymentPlatformFactory::INTERVAL_WEEK,
        PaymentPlatformFactory::INTERVAL_MONTH,
        PaymentPlatformFactory::INTERVAL_YEAR,
    ],
];
