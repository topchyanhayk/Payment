<?php

namespace App\Http\Controllers\Api\V1\Payment\Stripe;

use App\Exceptions\SubscriptionAlreadyPayedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\PaySubscriptionRequest;
use App\Http\Requests\Payment\Stripe\SessionCompleteRequest;
use App\Http\Requests\Payment\Stripe\WebhookCompleteRequest;
use App\Repositories\Read\Client\ClientReadRepositoryInterface;
use App\Repositories\Read\Subscription\SubscriptionReadRepositoryInterface;
use App\Services\Payment\Stripe\Dto\StripeWebhookDto;
use App\Services\Payment\Stripe\StripeBroker;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class StripeController extends Controller
{
    protected ClientReadRepositoryInterface $clientReadRepository;
    protected StripeBroker $stripeBroker;
    protected SubscriptionReadRepositoryInterface $subscriptionReadRepository;

    public function __construct(
        StripeBroker $stripeBroker,
        ClientReadRepositoryInterface $clientReadRepository,
        SubscriptionReadRepositoryInterface $subscriptionReadRepository
    ) {
        $this->clientReadRepository = $clientReadRepository;
        $this->stripeBroker = $stripeBroker;
        $this->subscriptionReadRepository = $subscriptionReadRepository;
    }

    public function paySubscription(PaySubscriptionRequest $request)
    {
        $subscription = $this->subscriptionReadRepository->getById($request->getId());

        if ($subscription->isPayed()) {
            return new SubscriptionAlreadyPayedException();
        }

        return view('payment.stripe.pay', ['sessionId' => $subscription->getPlatformSessionId()]);
    }

    public function sessionComplete(SessionCompleteRequest $request): RedirectResponse
    {
        $client = $this->clientReadRepository->getById($request->getClientId());

        return $this->redirect(
            $client->getRedirectUrl() .  '?' . http_build_query(['status' => $request->getStatus()])
        );
    }

    public function handleWebhookAction(WebhookCompleteRequest $request): JsonResponse
    {
        $stripeWebhookDto = new StripeWebhookDto(
            $request->getWebhookId(),
            $request->getEventType(),
            $request->getSubscriptionId(),
            $request->getObjectId()
        );

        $result = $this->stripeBroker->handleWebhookAction($stripeWebhookDto);

        return $this->response($result->toDataAsArray($request));
    }
}
