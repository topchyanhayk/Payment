<?php

namespace App\Models\Log;

use App\Models\Id;
use App\Services\Log\LogService;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Log
 * @package App\Models
 * @property Id $id
 * @property string|null $client_id
 * @property string $type
 * @property string|null $platform
 * @property string|null $request
 * @property string|null $response
 * @property string $status
 */
class Log extends Model
{
    use HasFactory;
    use UsesUuid;

    public $table = 'logs';
    public $fillable = [
        'client_id',
        'type',
        'platform',
        'request',
        'response',
        'status',
    ];

    public static function create(
        string $type,
        ?string $clientId = null,
        ?string $request = null,
        ?string $platform = null,
        string $status = LogService::STATUS_PENDING
    ): self
    {
        $entity = new static();

        $entity->setId(self::generateUuid());
        $entity->setType($type);
        $entity->setClient($clientId);
        $entity->setRequest($request);
        $entity->setPlatform($platform);
        $entity->setStatus($status);

        return $entity;
    }

    public function success(?string $response, ?string $request): self
    {
        $this->setStatus(LogService::STATUS_PROCESSED);
        $this->setResponse($response);
        if (!is_null($request)) {
            $this->setRequest($request);
        }

        return $this;
    }

    public function fail(?string $response, ?string $request): self
    {
        $this->setStatus(LogService::STATUS_FAILED);
        $this->setResponse($response);
        if (!is_null($request)) {
            $this->setRequest($request);
        }

        return $this;
    }

    public function setId(Id $id): void
    {
        $this->id = $id;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setClient(?string $clientId): void
    {
        $this->client_id = $clientId;
    }

    public function setRequest(?string $request): void
    {
        $this->request = $request;
    }

    public function setResponse(?string $response): void
    {
        $this->response = $response;
    }

    public function setPlatform(?string $platform): void
    {
        $this->platform = $platform;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getId(): Id
    {
        return new Id($this->id);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getClient(): ?string
    {
        return $this->client_id;
    }

    public function getRequest(): ?string
    {
        return $this->request;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function getPlatform(): ?string
    {
        return $this->platform;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
