<?php

namespace App\Repositories\Write\Client;

use App\Exceptions\SavingErrorException;
use App\Models\Client\Client;

class ClientWriteRepository implements ClientWriteRepositoryInterface
{
    public function save(Client $client): bool
    {
        if (!$client->save()) {
            throw new SavingErrorException();
        }
        return true;
    }
}
