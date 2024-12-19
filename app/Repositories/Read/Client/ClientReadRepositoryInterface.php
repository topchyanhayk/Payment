<?php

namespace App\Repositories\Read\Client;

use App\Models\Client\Client;

interface ClientReadRepositoryInterface
{
    public function getBySecret(string $secret): ?Client;
    public function getByName(string $name): ?Client;
    public function getById(string $id): ?Client;
}
