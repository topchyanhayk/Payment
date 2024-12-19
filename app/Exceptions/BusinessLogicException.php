<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

abstract class BusinessLogicException extends Exception
{
    const VALIDATION_FAILED = 600;
    const CLIENT_DOES_NOT_EXIST = 601;
    const GENERATE_CLIENT_TOKEN = 602;
    const PLAN_DOES_NOT_EXIST = 603;
    const PLAN_PLATFORM_DOES_NOT_EXIST = 604;
    const PLATFORM_DOES_NOT_EXIST = 605;
    const SAVING_ERROR = 606;
    const SUBSCRIPTION_DOES_NOT_EXIST = 607;
    const SUBSCRIPTION_ALREADY_PAYED = 608;
    const SUBSCRIPTION_NOT_CONFIRMED = 609;
    const CLIENT_ALREADY_EXIST = 610;

    const STRIPE_INVOICE_DOES_NOT_EXIST = 700;
    const STRIPE_SESSION_DOES_NOT_EXIST = 701;
    const STRIPE_SUBSCRIPTION_DOES_NOT_EXIST = 702;
    const STRIPE_SUBSCRIPTION_DOES_NOT_EXPIRED = 703;
    const STRIPE_SUBSCRIPTION_DOES_NOT_PAYED = 704;
    const STRIPE_SESSION_ALREADY_PAYED = 705;
    const STRIPE_SUBSCRIPTION_ALREADY_CANCELED = 706;
    const STRIPE_WRONG_TYPE_WEBHOOK = 707;
    const STRIPE_PLAN_CREATE_FAILED = 708;
    const STRIPE_CHECKOUT_SESSION_CREATE_FAILED = 709;
    const STRIPE_WEBHOOK_ALREADY_RECEIVED_AND_HANDLED = 710;

    private int $httpStatusCode = Response::HTTP_BAD_REQUEST;

    abstract public function getStatus(): int;
    abstract public function getStatusMessage(): string;

    public function getHttpStatusCode(): int
    {
        return $this->httpStatusCode;
    }

    public function setHttpStatusCode(int $code): self
    {
        $this->httpStatusCode = $code;
        return $this;
    }
}
