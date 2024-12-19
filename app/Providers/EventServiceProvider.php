<?php

namespace App\Providers;

use App\Events\Subscription\SubscriptionChargedSuccessEvent;
use App\Events\Subscription\SubscriptionChargeFailedEvent;
use App\Events\Subscription\SubscriptionPlanChangedSuccessEvent;
use App\Events\Subscription\SubscriptionPlanChangeFailedEvent;
use App\Listeners\Subscription\SendWebhookAboutSubscriptionChargedSuccess;
use App\Listeners\Subscription\SendWebhookAboutSubscriptionChargeFailed;
use App\Listeners\Subscription\SendWebhookAboutSubscriptionPlanChangedSuccess;
use App\Listeners\Subscription\SendWebhookAboutSubscriptionPlanChangeFailed;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SubscriptionChargedSuccessEvent::class => [
            SendWebhookAboutSubscriptionChargedSuccess::class,
        ],
        SubscriptionChargeFailedEvent::class => [
            SendWebhookAboutSubscriptionChargeFailed::class,
        ],
        SubscriptionPlanChangedSuccessEvent::class => [
            SendWebhookAboutSubscriptionPlanChangedSuccess::class,
        ],
        SubscriptionPlanChangeFailedEvent::class => [
            SendWebhookAboutSubscriptionPlanChangeFailed::class,
        ],
    ];

    public function boot()
    {
        //
    }
}
