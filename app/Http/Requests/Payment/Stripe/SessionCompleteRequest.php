<?php

namespace App\Http\Requests\Payment\Stripe;

use Illuminate\Foundation\Http\FormRequest;

class SessionCompleteRequest extends FormRequest
{
    const CLIENT_ID = 'client';
    const STATUS = 'status';

    public function rules(): array
    {
        return [
            self::CLIENT_ID => [
                'required',
                'string',
            ],
            self::STATUS => [
                'required',
                'string',
            ],
        ];
    }

    public function getClientId(): string
    {
        return $this->get(self::CLIENT_ID);
    }

    public function getStatus(): string
    {
        return $this->get(self::STATUS);
    }
}
