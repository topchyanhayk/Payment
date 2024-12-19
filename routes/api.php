<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->namespace('Api\V1')
    ->group(function () {
        Route::middleware('auth')
            ->group(function () {

                Route::prefix('clients')
                    ->as('clients.')
                    ->group(function () {

                        Route::post('{client}/plans', 'Plan\PlanController@create')
                            ->name('plans.create');
                        Route::post('{client}/subscriptions', 'Subscription\SubscriptionController@create')
                            ->name('subscriptions.create');
                    });

                Route::prefix('subscriptions')
                    ->namespace('Subscription')
                    ->as('subscriptions.')
                    ->group(function () {

                        Route::get('/{subscription}', 'SubscriptionController@get')
                            ->name('get');
                        Route::get('/{subscription}/pay', 'SubscriptionController@pay')
                            ->name('pay');
                        Route::get('/{subscription}/cancel', 'SubscriptionController@cancel')
                            ->name('cancel');
                        Route::put('/{subscription}', 'SubscriptionController@update')
                            ->name('update');
                    });
            });

        Route::prefix('payments')
            ->namespace('Payment')
            ->as('payments.')
            ->group(function () {

                Route::prefix('stripe')
                    ->namespace('Stripe')
                    ->as('stripe.')
                    ->group(function () {

                        Route::get('/session/complete', 'StripeController@sessionComplete')
                            ->name('session.complete');
                        Route::any('/webhook', 'StripeController@handleWebhookAction')
                            ->name('webhook');
                        Route::get('/pay', 'StripeController@paySubscription')
                            ->name('pay');
                    });
            });
});
