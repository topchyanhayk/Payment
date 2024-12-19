<?php

namespace App\Providers;

use App\Services\Dispatchers\EventDispatcherInterface;
use App\Services\Dispatchers\IlluminateEventDispatcher;
use Illuminate\Support\ServiceProvider;

class EventDispatcherServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(
            EventDispatcherInterface::class,
            IlluminateEventDispatcher::class
        );
    }

    public function boot(): void
    {
        //
    }
}
