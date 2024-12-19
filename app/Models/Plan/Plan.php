<?php

namespace App\Models\Plan;

use App\Models\Client\Client;
use App\Models\Id;
use App\Models\Subscription\Subscription;
use App\Services\Plan\Dto\PlanEntityDto;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * Class Plan
 *
 * @property Id $id
 * @property string $client_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PlanPlatform[]|Collection $platforms
 * @property-read Client $client
 * @property-read PlanEntityDto[] $paymentEntity
 * @method static Plan|null find($id)
 * @property-read Collection $subscriptions
 */
class Plan extends Model
{
    use UsesUuid;
    use HasFactory;

    protected $table = 'plans';

    protected $fillable = [
        'client_id',
    ];

    public static function createByClient(string $clientId): self
    {
        $entity = new static();

        $entity->setId(self::generateUuid());
        $entity->setClient($clientId);

        return $entity;
    }

    public function platforms(): HasMany
    {
        return $this->hasMany(PlanPlatform::class, 'plan_id');
    }

    public function getPlatforms(): Collection
    {
        return $this->platforms;
    }

    public function subscriptions(): HasManyThrough
    {
        return $this->hasManyThrough(Subscription::class, PlanPlatform::class);
    }

    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setId(Id $id): void
    {
        $this->id = $id;
    }

    public function setClient(string $clientId): void
    {
        $this->client_id = $clientId;
    }

    public function setPaymentEntity(PlanEntityDto $dto): void
    {
        $this->paymentEntity = $this->paymentEntity ? array_push($this->paymentEntity, $dto) : [ $dto ];
    }

    public function getId(): Id
    {
        return new Id($this->id);
    }

    public function getClientId(): string
    {
        return $this->client_id;
    }

    public function getPaymentEntity(): array
    {
        return $this->paymentEntity;
    }
}
