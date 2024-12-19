<?php

namespace App\Http\Requests\Payment\Stripe;

use Illuminate\Foundation\Http\FormRequest;

class WebhookCompleteRequest extends FormRequest
{
    const WEBHOOK_ID = 'id';
    const EVENT_TYPE = 'type';
    const SUBSCRIPTION_ID = 'data.object.subscription';
    const OBJECT_ID = 'data.object.id';

    public function rules(): array
    {
        return [
            self::WEBHOOK_ID => [
                'required',
                'string',
            ],
            self::EVENT_TYPE => [
                'required',
                'string',
            ],
            self::SUBSCRIPTION_ID => [
                'nullable',
                'string',
            ],
            self::OBJECT_ID => [
                'required',
                'string',
            ],
        ];
    }

    public function getWebhookId(): string
    {
        return $this->get(self::WEBHOOK_ID);
    }

    public function getEventType(): string
    {
        return $this->get(self::EVENT_TYPE);
    }

    public function getSubscriptionId(): ?string
    {
        return $this->input(self::SUBSCRIPTION_ID);
    }

    public function getObjectId(): string
    {
        return $this->input(self::OBJECT_ID);
    }
}
