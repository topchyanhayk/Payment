<?php

namespace App\Services\Subscription\Dto;

class SubscriptionEntityDto
{
    public ?string $id;
    public ?string $payUrl;
    public ?string $sessionId;
    public ?string $status;
    public ?int $startDate;
    public ?int $canceledAt;
    public ?int $currentPeriodStart;
    public ?int $currentPeriodEnd;
    public ?string $planId;

    public function __construct(
        ?string $id,
        ?string $payUrl = null,
        ?string $sessionId = null,
        ?string $status = null,
        ?int $startDate = null,
        ?int $canceledAt = null,
        ?int $currentPeriodStart = null,
        ?int $currentPeriodEnd = null,
        ?string $planId = null
    ) {
        $this->id = $id;
        $this->payUrl = $payUrl;
        $this->sessionId = $sessionId;
        $this->status = $status;
        $this->startDate = $startDate;
        $this->canceledAt = $canceledAt;
        $this->currentPeriodStart = $currentPeriodStart;
        $this->currentPeriodEnd = $currentPeriodEnd;
        $this->planId = $planId;
    }
}
