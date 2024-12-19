<?php

namespace App\Exceptions;

class ClientAlreadyExistException extends BusinessLogicException
{
    public function getStatus(): int
    {
        return BusinessLogicException::CLIENT_ALREADY_EXIST;
    }

    public function getStatusMessage(): string
    {
        return __('errors.client_already_exist');
    }
}
