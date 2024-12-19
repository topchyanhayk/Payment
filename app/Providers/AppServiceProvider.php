<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            StripeClient::class, function () {
            return new StripeClient(config('payment.stripe.secretKey'));
        });
    }

    public function boot(): void
    {
        //
    }
}
