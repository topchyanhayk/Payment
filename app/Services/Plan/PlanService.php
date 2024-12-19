<?php

namespace App\Services\Plan;

use App\Models\Plan\Plan;
use App\Models\Plan\PlanPlatform;
use App\Repositories\Read\Plan\PlanReadRepositoryInterface;
use App\Repositories\Write\Plan\PlanWriteRepositoryInterface;
use App\Services\Factories\PaymentPlatformFactory;
use App\Services\Log\Dto\CreateLogDto;
use App\Services\Log\Dto\LogDto;
use App\Services\Log\LogService;
use App\Services\Plan\Dto\CreatePlanDto;
use App\Services\Plan\Presenters\PlanPresenter;
use Infrastructure\Services\Platforms\PaymentPlatformWithPlanInterface;
use Exception;

class PlanService
{
    protected PlanWriteRepositoryInterface $planWriteRepository;
    protected PlanReadRepositoryInterface $planReadRepository;
    protected PaymentPlatformFactory $paymentPlatformFactory;
    protected LogService $logService;

    public function __construct(
        PlanWriteRepositoryInterface $planWriteRepository,
        PlanReadRepositoryInterface $planReadRepository,
        PaymentPlatformFactory $paymentPlatformFactory,
        LogService $logService
    ) {
        $this->planWriteRepository = $planWriteRepository;
        $this->planReadRepository = $planReadRepository;
        $this->paymentPlatformFactory = $paymentPlatformFactory;
        $this->logService = $logService;
    }

    public function create(CreatePlanDto $dto): PlanPresenter
    {
        $log = $this->logService->create(
            new CreateLogDto(LogService::CREATE_PLAN, $dto->clientId, json_encode($dto))
        );

        $plan = Plan::createByClient($dto->clientId);
        $this->planWriteRepository->save($plan);

        foreach (config('payment.platforms') as $platform) {
            $paymentPlatformService = $this->paymentPlatformFactory->getPaymentPlatformClient($platform);

            if ($paymentPlatformService instanceof PaymentPlatformWithPlanInterface) {
                $platformLog = $this->logService->create(
                    new CreateLogDto(
                        LogService::PUBLISH_PLAN,
                        $dto->clientId,
                        json_encode($dto),
                        $platform
                    )
                );

                try {
                    $paymentPlatformPlan = $paymentPlatformService->publishPlan($dto);
                    $plan->setPaymentEntity($paymentPlatformPlan);
                    $this->logService->success($platformLog, json_encode($paymentPlatformPlan));

                } catch (Exception $exception) {
                    $response = new LogDto($exception->getCode(), $exception->getMessage());
                    $this->logService->fail($platformLog, json_encode($response));

                    throw $exception;
                }

                $planPlatform = PlanPlatform::createByClient(
                    $plan->getId()->getValue(),
                    $platform, $paymentPlatformPlan->id
                );
                $this->planWriteRepository->savePlanPlatform($planPlatform);
            }
        }

        $this->logService->success($log, json_encode($plan));

        return new PlanPresenter($plan);
    }
}
