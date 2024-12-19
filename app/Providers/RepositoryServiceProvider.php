<?php

namespace App\Providers;

use App\Repositories\Read\Client\ClientReadRepository;
use App\Repositories\Read\Client\ClientReadRepositoryInterface;
use App\Repositories\Read\Plan\PlanReadRepository;
use App\Repositories\Read\Plan\PlanReadRepositoryInterface;
use App\Repositories\Read\Subscription\SubscriptionReadRepository;
use App\Repositories\Read\Subscription\SubscriptionReadRepositoryInterface;
use App\Repositories\Read\Webhook\WebhookReadRepository;
use App\Repositories\Read\Webhook\WebhookReadRepositoryInterface;
use App\Repositories\Write\Client\ClientWriteRepository;
use App\Repositories\Write\Client\ClientWriteRepositoryInterface;
use App\Repositories\Write\Log\LogWriteRepository;
use App\Repositories\Write\Log\LogWriteRepositoryInterface;
use App\Repositories\Write\Plan\PlanWriteRepository;
use App\Repositories\Write\Plan\PlanWriteRepositoryInterface;
use App\Repositories\Write\Subscription\SubscriptionWriteRepository;
use App\Repositories\Write\Subscription\SubscriptionWriteRepositoryInterface;
use App\Repositories\Write\Webhook\WebhookWriteRepository;
use App\Repositories\Write\Webhook\WebhookWriteRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //Client aggregate
        //write
        $this->app->singleton(
            ClientWriteRepositoryInterface::class,
            ClientWriteRepository::class
        );

        //read
        $this->app->singleton(
            ClientReadRepositoryInterface::class,
            ClientReadRepository::class
        );

        //Plan aggregate
        //write
        $this->app->singleton(
            PlanWriteRepositoryInterface::class,
            PlanWriteRepository::class
        );

        //read
        $this->app->singleton(
            PlanReadRepositoryInterface::class,
            PlanReadRepository::class
        );

        //Subscription aggregate
        //write
        $this->app->singleton(
            SubscriptionWriteRepositoryInterface::class,
            SubscriptionWriteRepository::class
        );

        //read
        $this->app->singleton(
            SubscriptionReadRepositoryInterface::class,
            SubscriptionReadRepository::class
        );

        //Log aggregate
        //write
        $this->app->singleton(
            LogWriteRepositoryInterface::class,
            LogWriteRepository::class
        );

        //Webhook aggregate
        //write
        $this->app->singleton(
            WebhookWriteRepositoryInterface::class,
            WebhookWriteRepository::class
        );

        //read
        $this->app->singleton(
            WebhookReadRepositoryInterface::class,
            WebhookReadRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
