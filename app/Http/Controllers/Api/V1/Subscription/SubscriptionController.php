<?php

namespace App\Http\Controllers\Api\V1\Subscription;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subscription\CancelSubscriptionRequest;
use App\Http\Requests\Subscription\GetSubscriptionRequest;
use App\Http\Requests\Subscription\PaySubscriptionRequest;
use App\Http\Requests\Subscription\CreateSubscriptionRequest;
use App\Http\Requests\Subscription\UpdateSubscriptionRequest;
use App\Models\Client\Client;
use App\Models\Subscription\Subscription;
use App\Services\Subscription\Dto\CreateSubscriptionDto;
use App\Services\Subscription\Dto\UpdateSubscriptionDto;
use App\Services\Subscription\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class SubscriptionController extends Controller
{
    protected SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function create(Client $client, CreateSubscriptionRequest $request): JsonResponse
    {
        $subscriptionCreateDto = new CreateSubscriptionDto(
            $request->getPlanId(),
            $request->getPlatform(),
            $request->getSubscriptionId(),
            $client->getId()->getValue(),
            $request->getEmail(),
            $request->getCountryCode(),
            $request->getCity(),
            $request->getPostalCode(),
            $request->getLine(),
            $request->getCompanyName(),
            $request->getPhoneNumber()
        );

        $result = $this->subscriptionService->create($subscriptionCreateDto);

        return $this->response($result->toDataAsArray($request));
    }

    public function update(Subscription $subscription, UpdateSubscriptionRequest $request): JsonResponse
    {
        $updateSubscriptionDto = new UpdateSubscriptionDto(
            $subscription->getId()->getValue(),
            $request->getPlanId()
        );

        $result = $this->subscriptionService->update($updateSubscriptionDto);

        return $this->response($result->toDataAsArray($request));
    }

    public function get(Subscription $subscription, GetSubscriptionRequest $request): JsonResponse
    {
        $result = $this->subscriptionService->get($subscription->getId()->getValue());

        return $this->response($result->toDataAsArray($request));
    }

    public function pay(Subscription $subscription, PaySubscriptionRequest $request): RedirectResponse
    {
        $url = $this->subscriptionService->pay($subscription->getId()->getValue());

        return $this->redirect($url);
    }

    public function cancel(Subscription $subscription, CancelSubscriptionRequest $request): JsonResponse
    {
        $result = $this->subscriptionService->cancel($subscription->getId()->getValue());

        return $this->response($result->toDataAsArray($request));
    }
}
