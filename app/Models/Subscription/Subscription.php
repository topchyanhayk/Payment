<?php

namespace App\Models\Subscription;

use App\Events\Subscription\SubscriptionChargedSuccessEvent;
use App\Events\Subscription\SubscriptionChargeFailedEvent;
use App\Events\Subscription\SubscriptionPlanChangedSuccessEvent;
use App\Events\Subscription\SubscriptionPlanChangeFailedEvent;
use App\Models\Id;
use App\Models\Plan\Plan;
use App\Models\Plan\PlanPlatform;
use App\Services\Subscription\Dto\SubscriptionEntityDto;
use App\Services\Subscription\SubscriptionService;
use App\Traits\EventsTrait;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Carbon;

/**
 * Class Subscription
 *
 * @property Id $id
 * @property string $plan_platform_id
 * @property string $status
 * @property string|null $client_subscription_id
 * @property string|null $platform_subscription_id
 * @property string|null $platform_session_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read SubscriptionEntityDto|null $paymentEntity
 * @property-read PlanPlatform $platform
 * @property-read Plan $plan
 * @method static Subscription|null find($id)
 */
class Subscription extends Model
{
    use UsesUuid;
    use HasFactory;
    use EventsTrait;

    protected $table = 'subscriptions';

    protected $fillable = [
        'plan_platform_id',
        'client_subscription_id',
        'status',
        'platform_subscription_id',
        'platform_session_id',
    ];

    public static function createByClient(
        string $planPlatformId,
        string $clientSubscriptionId,
        string $status = SubscriptionService::NEW
    ): self
    {
        $entity = new static();

        $entity->setId(self::generateUuid());
        $entity->setPlanPlatformId($planPlatformId);
        $entity->setClientSubscriptionId($clientSubscriptionId);
        $entity->setStatus($status);

        return $entity;
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(PlanPlatform::class, 'plan_platform_id');
    }

    public function getPlatform(): PlanPlatform
    {
        return $this->platform;
    }

    public function plan(): HasOneThrough
    {
        return $this->hasOneThrough(
            Plan::class,
            PlanPlatform::class,
            'id',
            'id',
            'plan_platform_id',
            'plan_id'
        );
    }

    public function getPlan(): Plan
    {
        return $this->plan;
    }

    public function setId(Id $id): void
    {
        $this->id = $id;
    }

    public function setPlatformSubscriptionId(?string $platformSubscriptionId): void
    {
        $this->platform_subscription_id = $platformSubscriptionId;
    }

    public function setPlatformSessionId(?string $sessionId): void
    {
        $this->platform_session_id = $sessionId;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setPlanPlatformId(string $planPlatformId): void
    {
        $this->plan_platform_id = $planPlatformId;
    }

    public function setClientSubscriptionId(string $clientSubscriptionId): void
    {
        $this->client_subscription_id = $clientSubscriptionId;
    }

    public function setPaymentEntity(SubscriptionEntityDto $dto): void
    {
        $this->paymentEntity = $dto;
    }

    public function setConfirmed(): void
    {
        $this->setStatus(SubscriptionService::CONFIRMED);
        $this->recordEvent(new SubscriptionChargedSuccessEvent($this));
    }

    public function setCanceled(): void
    {
        $this->setStatus(SubscriptionService::CANCELED);
        $this->recordEvent(new SubscriptionChargeFailedEvent($this));
    }

    public function setExpired(): void
    {
        $this->setStatus(SubscriptionService::EXPIRED);
        $this->recordEvent(new SubscriptionChargeFailedEvent($this));
    }

    public function isConfirmed(): bool
    {
        return $this->status === SubscriptionService::CONFIRMED;
    }

    public function isPayed(): bool
    {
        return $this->status !== SubscriptionService::NEW && $this->status !== SubscriptionService::PENDING;
    }

    public function getId(): Id
    {
        return new Id($this->id);
    }

    public function getPlatformSubscriptionId(): ?string
    {
        return $this->platform_subscription_id;
    }

    public function getPlatformSessionId(): ?string
    {
        return $this->platform_session_id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPlanPlatformId(): string
    {
        return $this->plan_platform_id;
    }

    public function getClientSubscriptionId(): string
    {
        return $this->client_subscription_id;
    }

    public function getPaymentEntity(): ?SubscriptionEntityDto
    {
        return $this->paymentEntity;
    }

    public function changePlan(string $planPlatformId): void
    {
        $this->setPlanPlatformId($planPlatformId);
        $this->recordEvent(new SubscriptionPlanChangedSuccessEvent($this));
    }

    public function changePlanFailed(): void
    {
        $this->recordEvent(new SubscriptionPlanChangeFailedEvent($this));
    }
}
