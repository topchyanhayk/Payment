<?php

namespace App\Services\Log\Dto;

class UpdateLogDto
{
    public ?string $type;
    public ?string $clientId;

    public function __construct(
        ?string $type = null,
        string $clientId = null
    ) {
        $this->type = $type;
        $this->clientId = $clientId;
    }
}
