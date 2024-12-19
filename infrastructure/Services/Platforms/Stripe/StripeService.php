<?php

namespace Infrastructure\Services\Platforms\Stripe;

use App\Exceptions\Stripe\StripeCheckoutSessionCreateFailedException;
use App\Exceptions\Stripe\StripeInvoiceDoesNotExistException;
use App\Exceptions\Stripe\StripePlanCreateFailedException;
use App\Exceptions\Stripe\StripeSessionAlreadyPayedException;
use App\Exceptions\Stripe\StripeSessionDoesNotExistException;
use App\Exceptions\Stripe\StripeSubscriptionAlreadyCanceledException;
use App\Exceptions\Stripe\StripeSubscriptionDoesNotExistException;
use App\Services\Factories\PaymentPlatformFactory;
use App\Services\Plan\Dto\CreatePlanDto;
use App\Services\Plan\Dto\PlanEntityDto;
use App\Services\Subscription\Dto\CreateSubscriptionDto;
use App\Services\Subscription\Dto\SubscriptionEntityDto;
use App\Services\Subscription\Dto\UpdateSubscriptionDto;
use Infrastructure\Services\Platforms\PaymentPlatformWithPlanInterface;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;
use Stripe\Invoice;
use Stripe\StripeClient;

class StripeService implements PaymentPlatformWithPlanInterface
{
    const SUBSCRIPTION_STATUS_PAID = 'active';
    const SUBSCRIPTION_STATUS_UNPAID = 'unpaid';
    const SUBSCRIPTION_STATUS_INCOMPLETE_EXPIRED = 'incomplete_expired';
    const SUBSCRIPTION_STATUS_CANCELED = 'canceled';

    const CURRENCY_EUR = 'eur';
    const CURRENCY_USD = 'usd';
    const CURRENCY_RUB = 'rub';

    const INTERVAL_DAY   = 'day';
    const INTERVAL_WEEK  = 'week';
    const INTERVAL_MONTH = 'month';
    const INTERVAL_YEAR  = 'year';

    const SESSION_STATUS_PAID = 'paid';

    protected StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function publishPlan(CreatePlanDto $dto): PlanEntityDto
    {
        try {
            $plan = $this->stripeClient->plans->create([
                'amount'         => $dto->price,
                'currency'       => $this->getCurrency($dto->currency),
                'interval'       => $this->getInterval($dto->type),
                'product'        => config('payment.stripe.productId'),
                'interval_count' => $dto->interval,
            ]);

        } catch (ApiErrorException $exception) {
            throw new StripePlanCreateFailedException();
        }

        return new PlanEntityDto($plan->id, PaymentPlatformFactory::STRIPE);
    }

    public function createSubscription(CreateSubscriptionDto $dto): SubscriptionEntityDto
    {
        $customer = $this->stripeClient->customers->all([
            'email' => $dto->email,
        ]);
        if (is_null($customer = $customer->first())) {
            try {
                $customer = $this->createCustomer($dto);
            } catch (ApiErrorException $exception) {
                dd($exception->getMessage());
            }
        }

        try {
            $session = $this->stripeClient->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'subscription_data' => [
                    'items' => [[
                        'plan' => $dto->planId,
                    ]],
                ],
                'customer' => $customer ? $customer->id : null,
                'success_url' => route(
                    'payments.stripe.session.complete',
                    [
                        'status' => 'success',
                        'client' => $dto->clientId
                    ]
                ),
                'cancel_url' =>  route(
                    'payments.stripe.session.complete',
                    [
                        'status' => 'cancel',
                        'client' => $dto->clientId
                    ]
                ),
            ]);

        } catch (ApiErrorException $exception) {
            throw new StripeCheckoutSessionCreateFailedException();
        }

        return new SubscriptionEntityDto(null, route('payments.stripe.pay'), $session->id);
    }

    public function updateSubscriptionPlan(UpdateSubscriptionDto $dto): SubscriptionEntityDto
    {
        $subscription = $this->stripeClient->subscriptions->retrieve($dto->subscriptionId);
        $this->stripeClient->subscriptions->update(
            $dto->subscriptionId,
            [
                'proration_behavior' => 'always_invoice',
                'items' => [[
                    'id' => $subscription->items->data[0]->id,
                    'plan' => $dto->planId,
                ]],
            ]
        );

        return new SubscriptionEntityDto($subscription->id);
    }

    public function getSubscription(string $subscriptionId): SubscriptionEntityDto
    {
        try {
            $subscription = $this->stripeClient->subscriptions->retrieve($subscriptionId);

        } catch (ApiErrorException $exception) {
            throw new StripeSubscriptionDoesNotExistException();
        }

        return new SubscriptionEntityDto(
            $subscription->id,
            route('payments.stripe.pay'),
            null,
            $subscription->status,
            $subscription->start_date,
            $subscription->canceled_at,
            $subscription->current_period_start,
            $subscription->current_period_end,
            $subscription->plan->id
        );
    }

    public function paySubscription(string $subscriptionId): SubscriptionEntityDto
    {
        $session = $this->getCheckoutSession($subscriptionId);

        if ($session->payment_status === self::SESSION_STATUS_PAID) {
            throw new StripeSessionAlreadyPayedException();
        }

        return new SubscriptionEntityDto(null, route('payments.stripe.pay'), $session->id);
    }

    public function cancelSubscription(string $subscriptionId): SubscriptionEntityDto
    {
        try {
            $subscription = $this->stripeClient->subscriptions->cancel($subscriptionId);
        } catch (ApiErrorException $exception) {
            throw new StripeSubscriptionAlreadyCanceledException();
        }

        return new SubscriptionEntityDto(
            $subscription->id,
            '',
            null,
            $subscription->status
        );
    }

    public function getCheckoutSession(string $id): Session
    {
        try {
            $session = $this->stripeClient->checkout->sessions->retrieve($id);

        } catch (ApiErrorException $exception) {
            throw new StripeSessionDoesNotExistException();
        }

        return $session;
    }

    public function getInvoice(string $id): Invoice
    {
        try {
            $invoice = $this->stripeClient->invoices->retrieve($id);

        } catch (ApiErrorException $exception) {
            throw new StripeInvoiceDoesNotExistException();
        }

        return $invoice;
    }

    public function getCurrency(string $currency): string
    {
        return config('payment.stripe.currency.' . $currency);
    }

    public function getInterval(string $type): string
    {
        return config('payment.stripe.interval.' . $type);
    }

    public function createCustomer(CreateSubscriptionDto $dto): Customer
    {
        $address = [
            'city' => $dto->city,
            'country' => $dto->countryCode,
            'postal_code' => $dto->postalCode,
            'line1' => $dto->line,
        ];

        return $this->stripeClient->customers->create([
            'address' => $address,
            'email' => $dto->email,
            'name' => $dto->companyName,
            'shipping' => [
                'address' => $address,
                'name' => $dto->companyName,
                'phone' => $dto->phoneNumber,
            ],
        ]);
    }
}
