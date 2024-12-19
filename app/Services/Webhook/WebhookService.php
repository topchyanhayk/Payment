<?php

namespace App\Services\Webhook;

use App\Models\Webhook\Webhook;
use App\Repositories\Read\Webhook\WebhookReadRepositoryInterface;
use App\Repositories\Write\Webhook\WebhookWriteRepositoryInterface;
use App\Services\Webhook\Dto\CreateWebhookDto;

class WebhookService
{
    const SUBSCRIPTION_CHARGED_SUCCESSFULLY = 'subscription.chargedSuccessfully';
    const SUBSCRIPTION_CHARGE_FAILED = 'subscription.chargeFailed';
    const SUBSCRIPTION_PLAN_CHANGED_SUCCESS = 'subscription.planChangedSuccess';
    const SUBSCRIPTION_PLAN_CHANGE_FAILED = 'subscription.planChangeFailed';

    const STATUS_RECEIVE_FROM_PLATFORM = 'receiveFromPlatform';
    const STATUS_SEND_TO_CLIENT = 'sendToClient';

    protected WebhookWriteRepositoryInterface $webhookWriteRepository;
    protected WebhookReadRepositoryInterface $webhookReadRepository;

    public function __construct(
        WebhookWriteRepositoryInterface $webhookWriteRepository,
        WebhookReadRepositoryInterface $webhookReadRepository
    ) {
        $this->webhookWriteRepository = $webhookWriteRepository;
        $this->webhookReadRepository = $webhookReadRepository;
    }

    public function create(CreateWebhookDto $dto): Webhook
    {
        $webhook = Webhook::create(
            $dto->platformId,
            $dto->platform,
            $dto->status
        );
        $this->webhookWriteRepository->save($webhook);

        return $webhook;
    }

    public function sentToClient(Webhook $webhook): Webhook
    {
        $webhook->setStatus(self::STATUS_SEND_TO_CLIENT);
        $this->webhookWriteRepository->save($webhook);

        return $webhook;
    }

    public function getWebhook(string $webhookId): ?Webhook
    {
        return $this->webhookReadRepository->getByPlatformId($webhookId);
    }

}
