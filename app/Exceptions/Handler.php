<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Spatie\QueryBuilder\Exceptions\InvalidQuery;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function report(Throwable $exception): void
    {
        parent::report($exception);
    }

    public function render($request, Throwable $exception): HttpFoundationResponse
    {
        $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $statusCode =  $exception->getCode();
        $details = [
            'message' => $exception->getMessage(),
        ];
        if ($exception instanceof ValidationException) {
            $httpCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            $statusCode = BusinessLogicException::VALIDATION_FAILED;
            $details['message'] = $exception->getMessage();
            foreach ($exception->errors() as $key => $error) {
                $details['errors'][$key] = $error[0] ?? 'Unknown error';
            }
        }
        if ($exception instanceof InvalidQuery) {
            $httpCode = Response::HTTP_UNPROCESSABLE_ENTITY;
            $statusCode = BusinessLogicException::VALIDATION_FAILED;
            $details['message'] = $exception->getMessage();
        }

        if ($exception instanceof NotFoundHttpException || $exception instanceof ModelNotFoundException) {
            $httpCode = Response::HTTP_NOT_FOUND;
            $statusCode = Response::HTTP_NOT_FOUND;
            $details['message'] = 'Not found';
        }

        if ($exception instanceof BadRequestHttpException) {
            $httpCode = Response::HTTP_BAD_REQUEST;
            $statusCode = Response::HTTP_BAD_REQUEST;
            $details['message'] = 'Bad Request';
        }

        if ($exception instanceof MethodNotAllowedHttpException) {
            $httpCode = Response::HTTP_METHOD_NOT_ALLOWED;
            $statusCode = Response::HTTP_METHOD_NOT_ALLOWED;
            $details['message'] = 'Method Not Allowed';
        }

        if ($exception instanceof AuthorizationException) {
            $httpCode = Response::HTTP_UNAUTHORIZED;
            $statusCode = Response::HTTP_UNAUTHORIZED;
            $details['message'] = 'Unauthorized';
        }

        if ($exception instanceof BusinessLogicException) {
            $httpCode = $exception->getHttpStatusCode();
            $statusCode = $exception->getStatus();
            $details['message'] = $exception->getStatusMessage();
        }

        $data = [
            'status'  => $statusCode,
            'errors' => $details,
        ];

        if ($httpCode === Response::HTTP_INTERNAL_SERVER_ERROR && !config('app.debug')) {
            $data['errors'] = [
                'message' => 'Server error',
            ];
        }

        return response()->json($data, $httpCode);
    }
}
