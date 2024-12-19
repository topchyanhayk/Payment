<?php

namespace App\Repositories\Write\Log;

use App\Models\Log\Log;

interface LogWriteRepositoryInterface
{
    public function save(Log $log): bool;
}
