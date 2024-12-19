<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class PaySubscriptionRequest extends FormRequest
{
    const ID = 'id';

    public function rules(): array
    {
        return [
            self::ID => [
                'required',
                'string',
            ],
        ];
    }

    public function getId(): string
    {
        return $this->get(self::ID);
    }
}
