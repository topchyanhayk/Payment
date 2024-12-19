<?php

namespace App\Services\Dispatchers;

/**
 * Interface EventDispatcherInterface
 * @package App\Services\Dispatchers
 */
interface EventDispatcherInterface
{
    public function dispatchAll(array $events): void;
    public function dispatch($event): void;
}
