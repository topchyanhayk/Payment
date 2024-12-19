<?php

namespace App\Repositories\Write\Log;

use App\Exceptions\SavingErrorException;
use App\Models\Log\Log;

class LogWriteRepository implements LogWriteRepositoryInterface
{
    public function save(Log $log): bool
    {
        if (!$log->save()) {
            throw new SavingErrorException();
        }
        return true;
    }
}
