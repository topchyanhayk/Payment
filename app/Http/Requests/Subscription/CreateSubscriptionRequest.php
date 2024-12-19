<?php

namespace App\Http\Requests\Subscription;

use App\Models\Subscription\Subscription;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSubscriptionRequest extends FormRequest
{
    const CLIENT_SUBSCRIPTION_ID = 'subscriptionId';
    const CLIENT_PLAN_ID = 'planId';
    const PLATFORM = 'platform';
    const EMAIL = 'email';
    const COUNTRY_CODE = 'countryCode';
    const CITY = 'city';
    const POSTAL_CODE = 'postalCode';
    const LINE = 'line';
    const COMPANY_NAME = 'companyName';
    const PHONE_NUMBER = 'phoneNumber';

    public function authorize(): bool
    {
        $client = $this->route('client');

        return $this->user()->can('create', [Subscription::class, $client]);
    }

    public function rules(): array
    {
        return [
            self::CLIENT_PLAN_ID => [
                'required',
                'string',
            ],
            self::CLIENT_SUBSCRIPTION_ID => [
                'required',
                'string',
            ],
            self::PLATFORM => [
                'required',
                'string',
                Rule::in(config('payment.platforms')),
            ],
            self::EMAIL => [
                'nullable',
                'email',
            ],
            self::LINE => [
                'nullable',
                'string',
            ],
            self::COUNTRY_CODE => [
                'nullable',
                'string',
            ],
            self::CITY => [
                'nullable',
                'string',
            ],
            self::POSTAL_CODE => [
                'nullable',
                'string',
            ],
            self::COMPANY_NAME => [
                'nullable',
                'string',
            ],
            self::PHONE_NUMBER => [
                'nullable',
                'string',
            ],
        ];
    }

    public function getPlanId(): string
    {
        return $this->get(self::CLIENT_PLAN_ID);
    }

    public function getPlatform(): string
    {
        return $this->get(self::PLATFORM);
    }

    public function getSubscriptionId(): string
    {
        return $this->get(self::CLIENT_SUBSCRIPTION_ID);
    }

    public function getEmail(): ?string
    {
        return $this->get(self::EMAIL);
    }

    public function getCountryCode(): ?string
    {
        return $this->get(self::COUNTRY_CODE);
    }

    public function getCity(): ?string
    {
        return $this->get(self::CITY);
    }

    public function getPostalCode(): ?string
    {
        return $this->get(self::POSTAL_CODE);
    }

    public function getLine(): ?string
    {
        return $this->get(self::LINE);
    }

    public function getCompanyName(): ?string
    {
        return $this->get(self::COMPANY_NAME);
    }

    public function getPhoneNumber(): ?string
    {
        return $this->get(self::PHONE_NUMBER);
    }
}
