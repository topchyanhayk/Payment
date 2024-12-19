<?php

namespace App\Models\Webhook;

use App\Models\Id;
use App\Services\Webhook\WebhookService;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Webhook
 * @package App\Models\Webhook
 * @property Id $id
 * @property string $platform_id
 * @property string|null $platform
 * @property string $status
 */
class Webhook extends Model
{
    use HasFactory;
    use UsesUuid;

    public $table = 'webhooks-receive';
    public $fillable = [
        'platform_id',
        'platform',
        'status',
    ];

    public static function create(
        string $platformId,
        string $platform,
        string $status = WebhookService::STATUS_RECEIVE_FROM_PLATFORM
    ): self
    {
        $entity = new static();

        $entity->setId(self::generateUuid());
        $entity->setPlatformId($platformId);
        $entity->setPlatform($platform);
        $entity->setStatus($status);

        return $entity;
    }

    public function setId(Id $id): void
    {
        $this->id = $id;
    }

    public function setPlatformId(string $platformId): void
    {
        $this->platform_id = $platformId;
    }

    public function setPlatform(string $platform): void
    {
        $this->platform = $platform;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getPlatformId(): string
    {
        return $this->platform_id;
    }

    public function getPlatform(): string
    {
        return $this->platform;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
}
