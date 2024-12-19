<?php

namespace App\Jobs;

use App\Models\Client\Client;
use App\Models\Log\Log;
use App\Notifications\SendWebhookFailedNotification;
use App\Services\Log\Dto\LogDto;
use App\Services\Log\LogService;
use Exception;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\RequestOptions;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\JsonResponse;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $uuid;
    private string $kind;
    private Client $client;
    private Log $log;

    public $tries = 5;

    public function __construct(
        string $uuid,
        string $kind,
        Client $client,
        Log $log
    ) {
        $this->uuid = $uuid;
        $this->kind = $kind;
        $this->client = $client;
        $this->log = $log;
    }

    public function backoff(): array
    {
        return [1, 5, 30, 60, 180];
    }

    public function failed()
    {
        if ($this->job->attempts() === $this->tries) {
            $this->client->notify(new SendWebhookFailedNotification($this->uuid, $this->kind));
        }
    }

    public function handle(HttpClient $httpClient, LogService $logService)
    {
        try {
            $request = [
                RequestOptions::HEADERS => [
                    'token' => $this->client->getSecret(),
                ],
                RequestOptions::JSON => [
                    'id' => $this->uuid,
                    'kind' => $this->kind,
                ],
            ];

            $response = $httpClient->post($this->client->getWebhookUrl(), $request);

            $request['url'] = $this->client->getWebhookUrl();
            $responseLog = new LogDto($response->getStatusCode());

            if ($response->getStatusCode() !== JsonResponse::HTTP_OK) {
                $logService->fail(
                    $this->log,
                    json_encode($responseLog),
                    json_encode($request)
                );
            } else {
                $logService->success(
                    $this->log,
                    json_encode($responseLog),
                    json_encode($request)
                );
            }

        } catch (Exception $exception) {
            $responseLog = new LogDto($exception->getCode(), $exception->getMessage());
            $logService->fail(
                $this->log,
                json_encode($responseLog),
                json_encode($request)
            );

            $this->job->markAsFailed();
        }
    }
}
