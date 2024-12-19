<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function response(array $data, int $statusCode = JsonResponse::HTTP_OK): JsonResponse
    {
        return response()->json($data, $statusCode);
    }

    public function redirect(string $url): RedirectResponse
    {
        return redirect()->to($url);
    }
}
