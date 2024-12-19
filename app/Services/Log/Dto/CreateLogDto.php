<?php

namespace App\Services\Log\Dto;

use App\Services\Log\LogService;

class CreateLogDto
{
    public string $type;
    public ?string $clientId;
    public ?string $request;
    public ?string $platform;
    public string $status;

    public function __construct(
        string $type,
        ?string $clientId = null,
        ?string $request = null,
        ?string $platform = null,
        string $status = LogService::STATUS_PENDING
    ) {
        $this->type = $type;
        $this->clientId = $clientId;
        $this->request = $request;
        $this->platform = $platform;
        $this->status = $status;
    }
}
