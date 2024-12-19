<?php

namespace App\Repositories\Read\Client;

use App\Exceptions\ClientDoesNotExistException;
use App\Models\Client\Client;

class ClientReadRepository implements ClientReadRepositoryInterface
{
    public function getBySecret(string $secret): ?Client
    {
        return Client::where('secret', $secret)->first();
    }

    public function getByName(string $name): ?Client
    {
        return Client::where('name', $name)->first();
    }

    public function getById(string $id): ?Client
    {
        $client = Client::find($id);
        if (is_null($client)) {
            throw new ClientDoesNotExistException();
        }

        return $client;
    }
}
