<?php

namespace App\Http\Requests\Plan;

use App\Models\Plan\Plan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePlanRequest extends FormRequest
{
    const NAME = 'name';
    const TYPE = 'type';
    const PRICE = 'price';
    const CURRENCY = 'currency';
    const INTERVAL_COUNT = 'intervalCount';

    public function authorize(): bool
    {
        $client = $this->route('client');

        return $this->user()->can('create', [Plan::class, $client]);
    }

    public function rules(): array
    {
        return [
            self::NAME => [
                'required',
                'string',
            ],
            self::TYPE => [
                'required',
                'string',
                Rule::in(config('payment.available_subscription_intervals')),
            ],
            self::PRICE => [
                'required',
                'integer',
            ],
            self::CURRENCY => [
                'required',
                'string',
                Rule::in(config('payment.available_currencies')),
            ],
            self::INTERVAL_COUNT => [
                'nullable',
                'integer',
            ],
        ];
    }

    public function getName(): string
    {
        return $this->get(self::NAME);
    }

    public function getType(): string
    {
        return $this->get(self::TYPE);
    }

    public function getPrice(): string
    {
        return $this->get(self::PRICE);
    }

    public function getCurrency(): string
    {
        return $this->get(self::CURRENCY);
    }

    public function getIntervalCount(): int
    {
        return $this->get(self::INTERVAL_COUNT) ?? 1;
    }
}
