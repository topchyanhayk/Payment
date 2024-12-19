<?php

namespace App\Services\Dispatchers;

use Illuminate\Events\Dispatcher;

/**
 * Laravel related implementation for events
 *
 * Class IlluminateEventDispatcher
 * @package App\Services\Dispatchers
 */
class IlluminateEventDispatcher implements EventDispatcherInterface
{
    private Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function dispatchAll(array $events): void
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }

    public function dispatch($event): void
    {
        $this->dispatcher->dispatch($event);
    }
}
