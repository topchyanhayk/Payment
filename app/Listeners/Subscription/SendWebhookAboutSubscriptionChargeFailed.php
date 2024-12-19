<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\SubscriptionChargeFailedEvent;
use App\Jobs\SendWebhook;
use App\Services\Log\Dto\CreateLogDto;
use App\Services\Log\LogService;
use App\Services\Webhook\WebhookService;

class SendWebhookAboutSubscriptionChargeFailed
{
    protected LogService $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function handle(SubscriptionChargeFailedEvent $event)
    {
        $client = $event->subscription->getPlan()->getClient();
        $log = $this->logService->create(
            new CreateLogDto(LogService::SEND_WEBHOOK_SUBSCRIPTION_CHARGE_FAILED, $client->getId()->getValue())
        );

        SendWebhook::dispatch(
            $event->subscription->getId()->getValue(),
            WebhookService::SUBSCRIPTION_CHARGE_FAILED,
            $client,
            $log
        );
    }
}
