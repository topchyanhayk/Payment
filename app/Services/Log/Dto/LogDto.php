<?php

namespace App\Services\Log\Dto;

class LogDto
{
    public ?string $statusCode;
    public ?string $message;

    public function __construct(
        ?string $statusCode,
        string $message = null
    )
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
    }
}
