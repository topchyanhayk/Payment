<?php

namespace App\Models\Plan;

use App\Models\Id;
use App\Models\Subscription\Subscription;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class PlanPlatform
 *
 * @property Id $id
 * @property string $plan_id
 * @property string $platform_type
 * @property string $platform_plan_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Plan $plan
 * @property-read Collection $subscriptions
 */
class PlanPlatform extends Model
{
    use UsesUuid;
    use HasFactory;

    protected $table = 'plan_platforms';

    protected $fillable = [
        'plan_id',
        'platform_type',
        'platform_plan_id',
    ];

    public static function createByClient(
        string $planId,
        string $platform,
        string $paymentPlatformPlanId
    ): self
    {
        $entity = new static();

        $entity->setId(self::generateUuid());
        $entity->setPlanId($planId);
        $entity->setPlatformType($platform);
        $entity->setPlatformPlanId($paymentPlatformPlanId);

        return $entity;
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function getPlan(): Plan
    {
        return $this->plan;
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    public function setId(Id $id): void
    {
        $this->id = $id;
    }

    public function setPlanId(string $planId): void
    {
        $this->plan_id = $planId;
    }

    public function setPlatformType(string $platform): void
    {
        $this->platform_type = $platform;
    }

    public function setPlatformPlanId(string $paymentPlatformPlanId): void
    {
        $this->platform_plan_id = $paymentPlatformPlanId;
    }

    public function getId(): Id
    {
        return new Id($this->id);
    }

    public function getPlanId(): string
    {
        return $this->plan_id;
    }

    public function getPlatformType(): string
    {
        return $this->platform_type;
    }

    public function getPlatformPlanId(): string
    {
        return $this->platform_plan_id;
    }
}
