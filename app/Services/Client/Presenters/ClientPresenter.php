<?php

namespace App\Services\Client\Presenters;

use App\Models\Client\Client;
use App\Services\Presenters\BasePresenter;
use App\Services\Presenters\ApiPresenterInterface;

/**
 * Class ClientPresenter
 * @package App\Services\Client\Presenters
 *
 * @property Client $client
 */
class ClientPresenter extends BasePresenter implements ApiPresenterInterface
{
    private Client $client;

    public function __construct(
        Client $client
    ) {
        $this->client = $client;
    }

    public function toArray($request = null): array
    {
        return [
            'id' => $this->client->getId()->getValue(),
            'name' => $this->client->getName(),
            'email' => $this->client->getEmail(),
            'redirectUrl' => $this->client->getRedirectUrl(),
            'webhookUrl' => $this->client->getWebhookUrl(),
        ];
    }
}
