<?php

namespace App\Exceptions;

class PlatformDoesNotExistException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return BusinessLogicException::PLATFORM_DOES_NOT_EXIST;
    }

    public function getStatusMessage(): string
    {
        return __('errors.platform_does_not_exist');
    }
}
