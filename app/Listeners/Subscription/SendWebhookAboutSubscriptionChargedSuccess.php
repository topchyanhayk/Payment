<?php

namespace App\Listeners\Subscription;

use App\Events\Subscription\SubscriptionChargedSuccessEvent;
use App\Jobs\SendWebhook;
use App\Services\Log\Dto\CreateLogDto;
use App\Services\Log\LogService;
use App\Services\Webhook\WebhookService;

class SendWebhookAboutSubscriptionChargedSuccess
{
    protected LogService $logService;

    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }

    public function handle(SubscriptionChargedSuccessEvent $event)
    {
        $client = $event->subscription->getPlan()->getClient();
        $log = $this->logService->create(
            new CreateLogDto(LogService::SEND_WEBHOOK_SUBSCRIPTION_CHARGED_SUCCESS, $client->getId()->getValue())
        );

        SendWebhook::dispatch(
            $event->subscription->getId()->getValue(),
            WebhookService::SUBSCRIPTION_CHARGED_SUCCESSFULLY,
            $client,
            $log
        );
    }
}
