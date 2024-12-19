<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;

/**
 * Class Id
 * @package App\Models
 * @property Id $value
 */
class Id
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function next(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }
}
