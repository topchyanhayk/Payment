<?php

namespace App\Services\Presenters;

interface ApiPresenterInterface
{
    public function toArray($request): array;
    public function toDataAsArray($request = null): array;
}
