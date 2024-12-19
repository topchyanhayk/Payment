<?php

namespace Infrastructure\Services\Platforms;

use App\Services\Subscription\Dto\CreateSubscriptionDto;
use App\Services\Subscription\Dto\SubscriptionEntityDto;
use App\Services\Subscription\Dto\UpdateSubscriptionDto;

interface PaymentPlatformInterface
{
    public function createSubscription(CreateSubscriptionDto $dto): SubscriptionEntityDto;
    public function getSubscription(string $subscriptionId): SubscriptionEntityDto;
    public function updateSubscriptionPlan(UpdateSubscriptionDto $dto): SubscriptionEntityDto;
    public function paySubscription(string $subscriptionId): SubscriptionEntityDto;
    public function cancelSubscription(string $subscriptionId): SubscriptionEntityDto;
    public function getCurrency(string $currency): string;
    public function getInterval(string $type): string;
}
