<?php

namespace App\Exceptions;

class GenerateClientTokenException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return BusinessLogicException::GENERATE_CLIENT_TOKEN;
    }

    public function getStatusMessage(): string
    {
        return __('errors.can_not_generate_token');
    }
}
