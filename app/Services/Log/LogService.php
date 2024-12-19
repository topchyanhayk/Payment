<?php

namespace App\Services\Log;

use App\Models\Log\Log;
use App\Repositories\Write\Log\LogWriteRepositoryInterface;
use App\Services\Log\Dto\CreateLogDto;
use App\Services\Log\Dto\UpdateLogDto;

class LogService
{
    const CREATE_PLAN = 'createPlan';
    const PUBLISH_PLAN = 'publishPlan';
    const CREATE_SUBSCRIPTION = 'createSubscription';
    const UPDATE_SUBSCRIPTION = 'updateSubscription';
    const PAY_SUBSCRIPTION = 'paySubscription';
    const GET_SUBSCRIPTION = 'getSubscription';
    const BILLING_REASON_SUBSCRIPTION_UPDATE = 'getSubscriptionPaymentSuccessAfterChangePlanToOld';
    const GET_SESSION = 'getSession';
    const CANCEL_SUBSCRIPTION = 'cancelSubscription';
    const SEND_WEBHOOK_SUBSCRIPTION_CHARGED_SUCCESS = 'sendWebhookSubscriptionChargedSuccess';
    const SEND_WEBHOOK_SUBSCRIPTION_CHARGE_FAILED = 'sendWebhookSubscriptionChargeFailed';
    const SEND_WEBHOOK_SUBSCRIPTION_PLAN_CHANGED_SUCCESS = 'sendWebhookSubscriptionPlanChangedSuccess';
    const SEND_WEBHOOK_SUBSCRIPTION_PLAN_CHANGE_FAILED = 'sendWebhookSubscriptionPlanChangeFailed';
    const GET_WEBHOOK = 'getWebhook';
    const GET_WEBHOOK_SUBSCRIPTION_CHARGED_SUCCESS = 'getWebhookSubscriptionChargedSuccess';
    const GET_WEBHOOK_SUBSCRIPTION_CHARGE_FAILED = 'getWebhookSubscriptionChargeFailed';
    const GET_WEBHOOK_CHECKOUT_SESSION_COMPLETED = 'getWebhookCheckoutSessionCompleted';
    const GET_WEBHOOK_SUBSCRIPTION_PLAN_CHANGE_FAILED = 'getWebhookSubscriptionPlanChangeFailed';
    const GET_WEBHOOK_SUBSCRIPTION_PLAN_CHANGED_SUCCESS = 'getWebhookSubscriptionPlanChangedSuccess';

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSED = 'success';
    const STATUS_FAILED = 'failed';

    protected LogWriteRepositoryInterface $logWriteRepository;

    public function __construct(
        LogWriteRepositoryInterface $logWriteRepository
    ) {
        $this->logWriteRepository = $logWriteRepository;
    }

    public function create(CreateLogDto $dto): Log
    {
        $log = Log::create(
            $dto->type,
            $dto->clientId,
            $dto->request,
            $dto->platform,
            $dto->status
        );
        $this->logWriteRepository->save($log);

        return $log;
    }

    public function success(
        Log $log,
        ?string $response = null,
        string $request = null
    ): void
    {
        $log->success(
            $response,
            $request
        );
        $this->logWriteRepository->save($log);
    }

    public function fail(
        Log $log,
        ?string $response = null,
        string $request = null
    ): void
    {
        $log->fail(
            $response,
            $request
        );
        $this->logWriteRepository->save($log);
    }

    public function update(
        Log $log,
        UpdateLogDto $dto
    ): void
    {
        if (!is_null($dto->type)) {
            $log->setType($dto->type);
        }
        if (!is_null($dto->clientId)) {
            $log->setClient($dto->clientId);
        }
        $this->logWriteRepository->save($log);
    }
}
