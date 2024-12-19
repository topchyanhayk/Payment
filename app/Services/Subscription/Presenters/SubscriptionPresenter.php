<?php

namespace App\Services\Subscription\Presenters;

use App\Models\Subscription\Subscription;
use App\Services\Presenters\BasePresenter;
use App\Services\Presenters\ApiPresenterInterface;

/**
 * Class SubscriptionPresenter
 * @package App\Services\Subscription\Presenters
 *
 * @property Subscription $subscription
 */
class SubscriptionPresenter extends BasePresenter implements ApiPresenterInterface
{
    private Subscription $subscription;

    public function __construct(
        Subscription $subscription
    ) {
        $this->subscription = $subscription;
    }

    public function toArray($request): array
    {
        return [
            'id' => $this->subscription->getId()->getValue(),
            'planPlatformId' => $this->subscription->getPlanPlatformId(),
            'status' => $this->subscription->getStatus(),
            'paymentEntity' => $this->subscription->getPaymentEntity(),
            'payUrl' => $this->getPayUrl($this->subscription->getId()->getValue())
        ];
    }

    private function getPayUrl(string $subscriptionId): string
    {
        return route(
            'subscriptions.pay',
            [
                'subscription' => $subscriptionId,
            ]);
    }
}
