<?php

namespace App\Models\Client;

use App\Models\Id;
use App\Models\Plan\Plan;
use App\Services\Client\Dto\CreateClientDto;
use App\Traits\UsesUuid;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

/**
 * Class Client
 *
 * @property Id $id
 * @property string $name
 * @property string $email
 * @property string $secret
 * @property string $webhookUrl
 * @property string $redirectUrl
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Plan[] $plans
 * @method static Client|null find($id)
 */
class Client extends Authenticatable
{
    use UsesUuid;
    use HasFactory;
    use Notifiable;

    protected $table = 'clients';

    protected $fillable = [
        'name',
        'email',
        'secret',
        'webhookUrl',
        'redirectUrl',
    ];

    protected $hidden = [
        'secret',
    ];

    public static function createByCommand(CreateClientDto $dto): self
    {
        $entity = new static();

        $entity->setId(self::generateUuid());
        $entity->setName($dto->name);
        $entity->setEmail($dto->email);
        $entity->setSecret($dto->secret);
        $entity->setWebhookUrl($dto->webhookUrl);
        $entity->setRedirectUrl($dto->redirectUrl);

        return $entity;
    }

    protected function plans(): HasMany
    {
        return $this->hasMany(Plan::class);
    }

    public function getPlans(): Collection
    {
        return $this->plans;
    }

    public function setId(Id $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    private function setSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    public function setWebhookUrl(string $webhookUrl): void
    {
        $this->webhookUrl = $webhookUrl;
    }

    public function setRedirectUrl(string $redirectUrl): void
    {
        $this->redirectUrl = $redirectUrl;
    }

    public function getId(): Id
    {
        return new Id($this->id);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSecret(): string
    {
        return $this->secret;
    }

    public function getWebhookUrl(): string
    {
        return $this->webhookUrl;
    }

    public function getRedirectUrl(): string
    {
        return $this->redirectUrl;
    }
}
