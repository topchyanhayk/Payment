<?php

namespace App\Services\Client;

use App\Exceptions\ClientAlreadyExistException;
use App\Exceptions\GenerateClientTokenException;
use App\Models\Client\Client;
use App\Repositories\Read\Client\ClientReadRepositoryInterface;
use App\Repositories\Write\Client\ClientWriteRepositoryInterface;
use App\Services\Client\Dto\CreateClientDto;
use App\Services\Client\Presenters\ClientPresenter;

class ClientService
{
    protected ClientWriteRepositoryInterface $clientWriteRepository;
    protected ClientReadRepositoryInterface $clientReadRepository;

    public function __construct(
        ClientWriteRepositoryInterface $clientWriteRepository,
        ClientReadRepositoryInterface $clientReadRepository
    ) {
        $this->clientWriteRepository = $clientWriteRepository;
        $this->clientReadRepository = $clientReadRepository;
    }

    public function create(CreateClientDto $dto): ClientPresenter
    {
        if (!is_null($this->clientReadRepository->getByName($dto->name))) {
            throw new ClientAlreadyExistException();
        }

        $client = Client::createByCommand($dto);
        $this->clientWriteRepository->save($client);

        return new ClientPresenter($client);
    }

    function generateToken(int $size): string
    {
        $iteration = 0;
        do {
            if($iteration > 100) {
                throw new GenerateClientTokenException();
            }
            $secret = bin2hex(random_bytes($size));
            $iteration++;
        } while ($this->clientReadRepository->getBySecret($secret));

        return $secret;
    }
}
