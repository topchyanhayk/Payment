<?php

namespace App\Repositories\Write\Client;

use App\Models\Client\Client;

interface ClientWriteRepositoryInterface
{
    public function save(Client $client): bool;
}
