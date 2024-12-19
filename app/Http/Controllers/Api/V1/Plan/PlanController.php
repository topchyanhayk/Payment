<?php

namespace App\Http\Controllers\Api\V1\Plan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Plan\CreatePlanRequest;
use App\Models\Client\Client;
use App\Services\Plan\Dto\CreatePlanDto;
use App\Services\Plan\PlanService;
use Illuminate\Http\JsonResponse;

class PlanController extends Controller
{
    protected PlanService $planService;

    public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }

    public function create(Client $client, CreatePlanRequest $request): JsonResponse
    {
        $planCreateDto = new CreatePlanDto(
            $client->getId()->getValue(),
            $request->getName(),
            $request->getType(),
            $request->getPrice(),
            $request->getCurrency(),
            $request->getIntervalCount()
        );

        $result = $this->planService->create($planCreateDto);

        return $this->response($result->toDataAsArray($request));
    }
}
