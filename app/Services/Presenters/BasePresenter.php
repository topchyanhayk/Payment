<?php

namespace App\Services\Presenters;

use Illuminate\Http\Resources\Json\JsonResource;

class BasePresenter extends JsonResource
{
    public function toDataAsArray($request = null): array
    {
        if (is_null($request)) {
            $request = request();
        }

        return $this->toArray($request);
    }
}
