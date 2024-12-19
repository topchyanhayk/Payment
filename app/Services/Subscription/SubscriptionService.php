<?php

namespace App\Services\Subscription;

use App\Exceptions\Stripe\StripeSubscriptionDoesNotExistException;
use App\Exceptions\SubscriptionAlreadyPayedException;
use App\Exceptions\SubscriptionNotConfirmedException;
use App\Models\Subscription\Subscription;
use App\Repositories\Read\Plan\PlanReadRepositoryInterface;
use App\Repositories\Read\Subscription\SubscriptionReadRepositoryInterface;
use App\Repositories\Write\Subscription\SubscriptionWriteRepositoryInterface;
use App\Services\Factories\PaymentPlatformFactory;
use App\Services\Log\Dto\CreateLogDto;
use App\Services\Log\Dto\LogDto;
use App\Services\Log\Dto\UpdateLogDto;
use App\Services\Log\LogService;
use App\Services\Subscription\Dto\CreateSubscriptionDto;
use App\Services\Subscription\Dto\UpdateSubscriptionDto;
use App\Services\Subscription\Presenters\SubscriptionPresenter;
use Exception;

class SubscriptionService
{
    const NEW = 'new';
    const PENDING = 'pending';
    const CONFIRMED = 'confirmed';
    const CANCELED = 'canceled';
    const EXPIRED = 'expired';

    protected SubscriptionWriteRepositoryInterface $subscriptionWriteRepository;
    protected SubscriptionReadRepositoryInterface $subscriptionReadRepository;
    protected PlanReadRepositoryInterface $planReadRepository;
    protected PaymentPlatformFactory $paymentPlatformFactory;
    protected LogService $logService;

    public function __construct(
        SubscriptionWriteRepositoryInterface $subscriptionWriteRepository,
        SubscriptionReadRepositoryInterface $subscriptionReadRepository,
        PlanReadRepositoryInterface $planReadRepository,
        PaymentPlatformFactory $paymentPlatformFactory,
        LogService $logService
    ) {
        $this->subscriptionWriteRepository = $subscriptionWriteRepository;
        $this->subscriptionReadRepository = $subscriptionReadRepository;
        $this->planReadRepository = $planReadRepository;
        $this->paymentPlatformFactory = $paymentPlatformFactory;
        $this->logService = $logService;
    }

    public function create(CreateSubscriptionDto $dto): SubscriptionPresenter
    {
        $log = $this->logService->create(
            new CreateLogDto(LogService::CREATE_SUBSCRIPTION, $dto->clientId, json_encode($dto))
        );

        $this->planReadRepository->getById($dto->planId);
        $planPlatform = $this->planReadRepository->getPlanPlatform($dto->planId, $dto->platform);

        $subscription = Subscription::createByClient(
            $planPlatform->getId()->getValue(),
            $dto->clientId,
            self::NEW
        );
        $this->subscriptionWriteRepository->save($subscription);

        $dto->planId = $planPlatform->getPlatformPlanId();
        $paymentPlatformService = $this->paymentPlatformFactory->getPaymentPlatformClient($dto->platform);

        $platformLog = $this->logService->create(
            new CreateLogDto(
                LogService::CREATE_SUBSCRIPTION,
                $dto->clientId,
                json_encode($dto),
                $dto->platform
            )
        );

        try {
            $paymentPlatformSubscription = $paymentPlatformService->createSubscription($dto);
            $this->logService->success($platformLog, json_encode($paymentPlatformSubscription));

        } catch (Exception $exception) {
            $response = new LogDto($exception->getCode(), $exception->getMessage());
            $this->logService->fail($platformLog, json_encode($response));

            throw $exception;
        }

        $subscription->setPlatformSubscriptionId($paymentPlatformSubscription->id);
        $subscription->setPlatformSessionId($paymentPlatformSubscription->sessionId);
        $subscription->setStatus(self::PENDING);
        $this->subscriptionWriteRepository->save($subscription);

        $this->logService->success($log, json_encode($subscription->toArray()));

        return new SubscriptionPresenter($subscription);
    }

    public function update(UpdateSubscriptionDto $dto): SubscriptionPresenter
    {
        $log = $this->logService->create(
            new CreateLogDto(LogService::UPDATE_SUBSCRIPTION, null, json_encode($dto))
        );

        $subscription = $this->subscriptionReadRepository->getById($dto->subscriptionId);
        if (!$subscription->isConfirmed()) {
            throw new SubscriptionNotConfirmedException();
        }

        $clientId = $subscription->getPlan()->getClientId();
        $this->logService->update($log, new UpdateLogDto(null, $clientId));

        $plan = $this->planReadRepository->getById($dto->planId);
        $planPlatform = $this->planReadRepository->getPlatform($plan, $subscription->getPlatform()->getPlatformType());

        $paymentPlatformService = $this->paymentPlatformFactory->getPaymentPlatformClient(
            $planPlatform->getPlatformType()
        );

        $dto = new UpdateSubscriptionDto(
            $subscription->getPlatformSubscriptionId(),
            $planPlatform->getPlatformPlanId()
        );

        $platformLog = $this->logService->create(
            new CreateLogDto(
                LogService::UPDATE_SUBSCRIPTION,
                $clientId,
                json_encode($dto),
                $planPlatform->getPlatformType()
            )
        );

        try {
            $paymentPlatformSubscription = $paymentPlatformService->updateSubscriptionPlan($dto);
            $this->logService->success($platformLog, json_encode($paymentPlatformSubscription));

        } catch (Exception $exception) {
            $response = new LogDto($exception->getCode(), $exception->getMessage());
            $this->logService->fail($platformLog, json_encode($response));

            throw $exception;
        }

        $this->logService->success($log, json_encode($subscription->toArray()));

        return new SubscriptionPresenter($subscription);
    }

    public function get(string $subscriptionId): SubscriptionPresenter
    {
        $log = $this->logService->create(
            new CreateLogDto(LogService::GET_SUBSCRIPTION, null, $subscriptionId)
        );

        $subscription = $this->subscriptionReadRepository->getById($subscriptionId);
        $clientId = $subscription->getPlan()->getClientId();
        $this->logService->update($log, new UpdateLogDto(null, $clientId));

        $platformSubscriptionId = $subscription->getPlatformSubscriptionId();
        $platform = $subscription->getPlatform()->getPlatformType();
        $paymentPlatformService = $this->paymentPlatformFactory->getPaymentPlatformClient($platform);

        $platformLog = $this->logService->create(
            new CreateLogDto(
                LogService::GET_SUBSCRIPTION,
                $clientId,
                $platformSubscriptionId,
                $platform
            )
        );

        if (!is_null($platformSubscriptionId)) {
            try {
                $paymentPlatformSubscription = $paymentPlatformService->getSubscription($platformSubscriptionId);
                $subscription->setPaymentEntity($paymentPlatformSubscription);
                $this->logService->success($platformLog, json_encode($paymentPlatformSubscription));

            } catch (Exception $exception) {
                $response = new LogDto($exception->getCode(), $exception->getMessage());
                $this->logService->fail($platformLog, json_encode($response));
            }
        }

        $this->logService->success($log, json_encode($subscription));

        return new SubscriptionPresenter($subscription);
    }

    public function pay(string $subscriptionId): string
    {
        $log = $this->logService->create(
            new CreateLogDto(LogService::PAY_SUBSCRIPTION, null, $subscriptionId)
        );

        $subscription = $this->subscriptionReadRepository->getById($subscriptionId);
        $clientId = $subscription->getPlan()->getClientId();
        $this->logService->update($log, new UpdateLogDto(null, $clientId));

        if ($subscription->isPayed()) {
            throw new SubscriptionAlreadyPayedException();
        }

        $sessionId = $subscription->getPlatformSessionId();
        $platform = $subscription->getPlatform()->getPlatformType();
        $paymentPlatformService = $this->paymentPlatformFactory->getPaymentPlatformClient($platform);

        $platformLog = $this->logService->create(
            new CreateLogDto(
                LogService::PAY_SUBSCRIPTION,
                $clientId,
                $sessionId,
                $platform
            )
        );

        try {
            $paymentPlatformSubscription = $paymentPlatformService->paySubscription($sessionId);
            $subscription->setPaymentEntity($paymentPlatformSubscription);
            $this->logService->success($platformLog, json_encode($paymentPlatformSubscription));

        } catch (Exception $exception) {
            $response = new LogDto($exception->getCode(), $exception->getMessage());
            $this->logService->fail($platformLog, json_encode($response));

            throw $exception;
        }

        $this->logService->success($log, json_encode($subscription));

        return $subscription->getPaymentEntity()->payUrl . '?' . http_build_query([
                    'id' => $subscriptionId
                ]);
    }

    public function cancel(string $subscriptionId): SubscriptionPresenter
    {
        $log = $this->logService->create(
            new CreateLogDto(LogService::CANCEL_SUBSCRIPTION, null, $subscriptionId)
        );

        $subscription = $this->subscriptionReadRepository->getById($subscriptionId);
        $clientId = $subscription->getPlan()->getClientId();
        $this->logService->update($log, new UpdateLogDto(null, $clientId));

        if (!$subscription->isConfirmed()) {
            throw new SubscriptionNotConfirmedException();
        }

        if (is_null($subscription->getPlatformSubscriptionId())) {
            throw new StripeSubscriptionDoesNotExistException();
        }

        $platform = $subscription->getPlatform()->getPlatformType();
        $paymentPlatformService = $this->paymentPlatformFactory->getPaymentPlatformClient($platform);

        $platformLog = $this->logService->create(
            new CreateLogDto(
                LogService::CANCEL_SUBSCRIPTION,
                $clientId,
                $subscription->getPlatformSubscriptionId(),
                $platform
            )
        );

        try {
            $paymentPlatformSubscription = $paymentPlatformService->cancelSubscription(
                $subscription->getPlatformSubscriptionId()
            );
            $this->logService->success($platformLog, json_encode($paymentPlatformSubscription));

        } catch (Exception $exception) {
            $response = new LogDto($exception->getCode(), $exception->getMessage());
            $this->logService->fail($platformLog, json_encode($response));

            throw $exception;
        }

        $subscription->setCanceled();
        $this->subscriptionWriteRepository->save($subscription);

        $this->logService->success($log, json_encode($subscription));

        return new SubscriptionPresenter($subscription);
    }
}
