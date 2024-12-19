<?php

namespace App\Services\Plan\Presenters;

use App\Models\Plan\Plan;
use App\Services\Presenters\BasePresenter;
use App\Services\Presenters\ApiPresenterInterface;

/**
 * Class PlanPresenter
 * @package App\Services\Plan\Presenters
 *
 * @property Plan $plan
 */
class PlanPresenter extends BasePresenter implements ApiPresenterInterface
{
    private Plan $plan;

    public function __construct(
        Plan $plan
    ) {
        $this->plan = $plan;
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->plan->getId()->getValue(),
            'clientId' => $this->plan->getClientId(),
            'paymentEntity' => $this->plan->getPaymentEntity(),
        ];
    }
}
