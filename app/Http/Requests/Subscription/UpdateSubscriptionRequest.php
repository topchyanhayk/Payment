<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateSubscriptionRequest extends FormRequest
{
    const CLIENT_PLAN_ID = 'planId';

    public function authorize(): bool
    {
        $subscription = $this->route('subscription');

        return Auth::user()->can('update', $subscription);
    }

    public function rules(): array
    {
        return [
            self::CLIENT_PLAN_ID => [
                'required',
                'string',
            ],
        ];
    }

    public function getPlanId(): string
    {
        return $this->get(self::CLIENT_PLAN_ID);
    }
}
