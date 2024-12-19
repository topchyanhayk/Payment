<?php

namespace App\Console\Commands\Client;

use App\Services\Client\ClientService;
use App\Services\Client\Dto\CreateClientDto;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateClient extends Command
{
    const TOKEN_SIZE = 64;

    protected $signature = 'create:client';

    protected $description = 'Create new client';

    public function handle(ClientService $clientService, ClientService $service): void
    {
        $name = $this->ask('Input client name');
        $email = $this->ask('Input client email');
        $redirectUrl = $this->ask('Input client redirectUrl');
        $webhookUrl = $this->ask('Input client webhookUrl');

        $data = [
            'name' => $name,
            'email' => $email,
            'redirectUrl' => $redirectUrl,
            'webhookUrl' => $webhookUrl,
        ];

        $rules = [
            'name' => 'required|string',
            'email' => 'required|email',
            'redirectUrl' => 'required|url',
            'webhookUrl' => 'required|url',
        ];

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $this->error($validator->messages());

            return;
        }

        try {
            $secret = $service->generateToken(self::TOKEN_SIZE);
            $clientDto = new CreateClientDto(
                $name,
                $email,
                $secret,
                $webhookUrl,
                $redirectUrl
            );

            $client = $clientService->create($clientDto);

            $this->info("The Client [$name] has been successfully created.");

        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}
