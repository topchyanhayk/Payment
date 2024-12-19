<?php

namespace App\Traits;

use App\Models\Id;

trait UsesUuid
{
    protected static function generateUuid(): Id
    {
        return Id::next();
    }

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return Id::class;
    }
}
