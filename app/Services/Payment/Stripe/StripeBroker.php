<?php

namespace App\Services\Payment\Stripe;

use App\Exceptions\Stripe\StripeSubscriptionDoesNotExistException;
use App\Exceptions\Stripe\StripeSubscriptionDoesNotExpiredException;
use App\Exceptions\Stripe\StripeSubscriptionDoesNotPayedException;
use App\Exceptions\Stripe\StripeWrongTypeWebhookException;
use App\Exceptions\Stripe\WebhookAlreadyReceivedAndHandledException;
use App\Models\Log\Log;
use App\Models\Subscription\Subscription;
use App\Models\Webhook\Webhook;
use App\Repositories\Read\Plan\PlanReadRepositoryInterface;
use App\Repositories\Read\Subscription\SubscriptionReadRepositoryInterface;
use App\Repositories\Write\Subscription\SubscriptionWriteRepositoryInterface;
use App\Services\Factories\PaymentPlatformFactory;
use App\Services\Log\Dto\CreateLogDto;
use App\Services\Log\Dto\LogDto;
use App\Services\Log\Dto\UpdateLogDto;
use App\Services\Log\LogService;
use App\Services\Payment\Stripe\Dto\StripeWebhookDto;
use App\Services\Subscription\Dto\SubscriptionEntityDto;
use App\Services\Subscription\Dto\UpdateSubscriptionDto;
use App\Services\Subscription\Presenters\SubscriptionPresenter;
use App\Services\Webhook\Dto\CreateWebhookDto;
use App\Services\Webhook\WebhookService;
use Exception;
use App\Services\Presenters\ApiPresenterInterface;
use Infrastructure\Services\Platforms\Stripe\StripeService;
use Stripe\Invoice;

class StripeBroker
{
    const INVOICE_PAYMENT_SUCCEEDED = 'invoice.payment_succeeded';
    const INVOICE_PAYMENT_FAILED = 'invoice.payment_failed';
    const CHECKOUT_SESSION_COMPLETED = 'checkout.session.completed';

    protected StripeService $stripeService;
    protected SubscriptionReadRepositoryInterface $subscriptionReadRepository;
    protected SubscriptionWriteRepositoryInterface $subscriptionWriteRepository;
    protected LogService $logService;
    protected PlanReadRepositoryInterface $planReadRepository;
    protected WebhookService $webhookService;

    protected ?Webhook $webhook;
    protected Log $log;

    public function __construct(
        StripeService $stripeService,
        SubscriptionReadRepositoryInterface $subscriptionReadRepository,
        SubscriptionWriteRepositoryInterface $subscriptionWriteRepository,
        LogService $logService,
        PlanReadRepositoryInterface $planReadRepository,
        WebhookService $webhookService
    ) {
        $this->stripeService = $stripeService;
        $this->subscriptionReadRepository = $subscriptionReadRepository;
        $this->subscriptionWriteRepository = $subscriptionWriteRepository;
        $this->logService = $logService;
        $this->planReadRepository = $planReadRepository;
        $this->webhookService = $webhookService;
    }

    public function handleWebhookAction(StripeWebhookDto $dto) : ApiPresenterInterface
    {
        $this->webhook = $this->webhookService->getWebhook($dto->webhookId);
        if (!$this->webhook) {
            $this->webhook = $this->webhookService->create(
                new CreateWebhookDto(
                    $dto->webhookId,
                    PaymentPlatformFactory::STRIPE
                )
            );
        } elseif ($this->webhook->getStatus() === WebhookService::STATUS_SEND_TO_CLIENT) {
            throw new WebhookAlreadyReceivedAndHandledException();
        }

        $this->log = $this->logService->create(
            new CreateLogDto(
                LogService::GET_WEBHOOK,
                null,
                json_encode($dto),
                PaymentPlatformFactory::STRIPE
            )
        );

        switch ($dto->type) {
            case self::INVOICE_PAYMENT_SUCCEEDED:
                return $this->handleInvoiceSucceededAction($dto);
            case self::INVOICE_PAYMENT_FAILED:
                return $this->handleInvoiceFailedAction($dto);
            case self::CHECKOUT_SESSION_COMPLETED:
                return $this->handleCheckoutSessionCompletedAction($dto->objectId);
            default:
                throw new StripeWrongTypeWebhookException();
        }
    }

    private function handleInvoiceSucceededAction(StripeWebhookDto $dto): ApiPresenterInterface
    {
        $invoice = $this->stripeService->getInvoice($dto->objectId);

        if (!is_null($invoice->subscription)) {
            return $this->handleSubscriptionSucceededAction($invoice);
        } else {
            throw new StripeWrongTypeWebhookException();
        }
    }

    private function handleInvoiceFailedAction(StripeWebhookDto $dto): ApiPresenterInterface
    {
        $invoice = $this->stripeService->getInvoice($dto->objectId);

        if (!is_null($invoice->subscription)) {
            return $this->handleSubscriptionFailedAction($invoice);
        } else {
            throw new StripeWrongTypeWebhookException();
        }
    }

    private function handleCheckoutSessionCompletedAction(string $objectId): SubscriptionPresenter
    {
        $this->logService->update(
            $this->log,
            new UpdateLogDto(LogService::GET_WEBHOOK_CHECKOUT_SESSION_COMPLETED)
        );

        $platformLog = $this->logService->create(
            new CreateLogDto(
                LogService::GET_SESSION,
                null,
                json_encode($objectId),
                PaymentPlatformFactory::STRIPE
            )
        );

        try {
            $session = $this->stripeService->getCheckoutSession($objectId);
            $this->logService->success($platformLog, json_encode($session));

        } catch (Exception $exception) {
            $response = new LogDto($exception->getCode(), $exception->getMessage());
            $this->logService->fail($platformLog, json_encode($response));

            throw $exception;
        }

        if (is_null($session->subscription)) {
            throw new StripeSubscriptionDoesNotExistException();
        }

        $subscription = $this->subscriptionReadRepository->getBySession($session->id);
        $subscription->setPlatformSubscriptionId($session->subscription);
        $this->subscriptionWriteRepository->save($subscription);

        $clientId = $subscription->getPlan()->getClientId();
        $this->logService->update($this->log, new UpdateLogDto(null, $clientId));
        $this->logService->success($this->log, json_encode($subscription));

        return new SubscriptionPresenter($subscription);
    }

    private function handleSubscriptionSucceededAction(Invoice $invoice): SubscriptionPresenter
    {
        $subscription = $this->subscriptionReadRepository->getByPlatformId($invoice->subscription);
        $clientId = $subscription->getPlan()->getClientId();
        $this->logService->update($this->log, new UpdateLogDto(null, $clientId));

        $stripeSubscription = $this->createLogAndGetStripeSubscription($invoice->subscription, $clientId);

        if ($stripeSubscription->status !== StripeService::SUBSCRIPTION_STATUS_PAID) {
            throw new StripeSubscriptionDoesNotPayedException();
        }

        if ($stripeSubscription->planId !== $subscription->getPlatform()->getPlatformPlanId()) {
            return $this->handleSubscriptionUpdatePlanSuccessAction($stripeSubscription, $subscription);
        }

        if ($invoice->billing_reason === Invoice::BILLING_REASON_SUBSCRIPTION_UPDATE) {
            $this->logService->update(
                $this->log,
                new UpdateLogDto(LogService::BILLING_REASON_SUBSCRIPTION_UPDATE)
            );

            $this->logService->success($this->log, json_encode($subscription));
            return new SubscriptionPresenter($subscription);
        }

        return $this->handleSubscriptionChargedSuccessAction($stripeSubscription, $subscription);
    }

    private function handleSubscriptionFailedAction(Invoice $invoice): SubscriptionPresenter
    {
        $subscription = $this->subscriptionReadRepository->getByPlatformId($invoice->subscription);
        $clientId = $subscription->getPlan()->getClientId();
        $this->logService->update($this->log, new UpdateLogDto(null, $clientId));

        $stripeSubscription = $this->createLogAndGetStripeSubscription($invoice->subscription, $clientId);

        if ($stripeSubscription->planId !== $subscription->getPlatform()->getPlatformPlanId()) {
            return $this->handleSubscriptionUpdatePlanFailedAction($stripeSubscription, $subscription);
        }

        return $this->handleSubscriptionChargeFailedAction($stripeSubscription, $subscription);
    }

    private function createLogAndGetStripeSubscription(string $subscriptionId, string $clientId): SubscriptionEntityDto
    {
        $platformLog = $this->logService->create(
            new CreateLogDto(
                LogService::GET_SUBSCRIPTION,
                $clientId,
                $subscriptionId,
                PaymentPlatformFactory::STRIPE
            )
        );

        try {
            $stripeSubscription = $this->stripeService->getSubscription($subscriptionId);
            $this->logService->success($platformLog, json_encode($stripeSubscription));

        } catch (Exception $exception) {
            $response = new LogDto($exception->getCode(), $exception->getMessage());
            $this->logService->fail($platformLog, json_encode($response));

            throw $exception;
        }

        return $stripeSubscription;
    }

    private function handleSubscriptionChargedSuccessAction(
        SubscriptionEntityDto $stripeSubscription,
        Subscription $subscription
    ): SubscriptionPresenter
    {
        $this->logService->update(
            $this->log,
            new UpdateLogDto(LogService::GET_WEBHOOK_SUBSCRIPTION_CHARGED_SUCCESS)
        );

        if ($stripeSubscription->status !== StripeService::SUBSCRIPTION_STATUS_PAID) {
            throw new StripeSubscriptionDoesNotPayedException();
        }

        $subscription->setConfirmed();
        $this->subscriptionWriteRepository->save($subscription);

        $this->webhookService->sentToClient($this->webhook);
        $this->logService->success($this->log, json_encode($subscription));

        return new SubscriptionPresenter($subscription);
    }

    private function handleSubscriptionUpdatePlanSuccessAction(
        SubscriptionEntityDto $stripeSubscription,
        Subscription $subscription
    ): SubscriptionPresenter
    {
        $this->logService->update(
            $this->log,
            new UpdateLogDto(LogService::GET_WEBHOOK_SUBSCRIPTION_PLAN_CHANGED_SUCCESS)
        );

        $planPlatform = $this->planReadRepository->getByPlatformId($stripeSubscription->planId);

        $subscription->changePlan($planPlatform->getId()->getValue());
        $this->subscriptionWriteRepository->save($subscription);

        $this->webhookService->sentToClient($this->webhook);
        $this->logService->success($this->log, json_encode($subscription));

        return new SubscriptionPresenter($subscription);
    }

    private function handleSubscriptionChargeFailedAction(
        SubscriptionEntityDto $stripeSubscription,
        Subscription $subscription
    ): SubscriptionPresenter
    {
        $this->logService->update(
            $this->log,
            new UpdateLogDto(LogService::GET_WEBHOOK_SUBSCRIPTION_CHARGE_FAILED)
        );

        if (
            $stripeSubscription->status !== StripeService::SUBSCRIPTION_STATUS_UNPAID &&
            $stripeSubscription->status !== StripeService::SUBSCRIPTION_STATUS_INCOMPLETE_EXPIRED &&
            $stripeSubscription->status !== StripeService::SUBSCRIPTION_STATUS_CANCELED
        ) {
            throw new StripeSubscriptionDoesNotExpiredException();
        }

        $subscription->setExpired();
        $this->subscriptionWriteRepository->save($subscription);

        $this->webhookService->sentToClient($this->webhook);
        $this->logService->success($this->log, json_encode($subscription));

        return new SubscriptionPresenter($subscription);
    }

    private function handleSubscriptionUpdatePlanFailedAction(
        SubscriptionEntityDto $stripeSubscription,
        Subscription $subscription
    ): SubscriptionPresenter
    {
        $this->logService->update(
            $this->log,
            new UpdateLogDto(LogService::GET_WEBHOOK_SUBSCRIPTION_PLAN_CHANGE_FAILED)
        );

        $dto = new UpdateSubscriptionDto(
            $subscription->getPlatformSubscriptionId(),
            $subscription->getPlatform()->getPlatformPlanId()
        );

        $platformLog = $this->logService->create(
            new CreateLogDto(
                LogService::UPDATE_SUBSCRIPTION,
                $subscription->getPlan()->getClientId(),
                json_encode($dto),
                $subscription->getPlatform()->getPlatformType()
            )
        );

        try {
            $paymentPlatformSubscription = $this->stripeService->updateSubscriptionPlan($dto);
            $this->logService->success($platformLog, json_encode($paymentPlatformSubscription));

        } catch (Exception $exception) {
            $response = new LogDto($exception->getCode(), $exception->getMessage());
            $this->logService->fail($platformLog, json_encode($response));

            throw $exception;
        }

        $subscription->changePlanFailed();
        $this->subscriptionWriteRepository->save($subscription);

        $this->webhookService->sentToClient($this->webhook);
        $this->logService->success($this->log, json_encode($subscription));

        return new SubscriptionPresenter($subscription);
    }
}
