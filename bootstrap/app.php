<?php

use App\Http\Responses\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn() => true);

        $exceptions->render(function (AccessDeniedHttpException $e) {
            return ApiResponse::forbidden($e->getMessage());
        });

        $exceptions->render(function (AuthenticationException $e) {
            return ApiResponse::unauthorized(
                $e->getMessage(),
                ['token' => ['Invalid token']]
            );
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e) {
            return ApiResponse::forbidden($e->getMessage());
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            $previous = $e->getPrevious();

            if ($previous instanceof ModelNotFoundException) {
                $model = $previous->getModel();
                $class = class_basename($model);
                $id = $previous->getIds()[0];

                return ApiResponse::notFound(
                    'Resource not found',
                    [
                        'resource' => $class,
                        'id' => $id,
                    ]
                );
            }

            $path = $request->path();
            return ApiResponse::notFound(
                'Route not found',
                ['route' => $path]
            );
        });

        $exceptions->render(function (RouteNotFoundException $e) {
            return ApiResponse::unauthorized(
                'Unauthorized',
                [
                    'token' => ['Token not found'],
                    'route' => [$e->getMessage()]
                ]
            );
        });

        $exceptions->render(function (ValidationException $e) {
            return ApiResponse::validationError(
                'Validation Error',
                $e->errors()
            );
        });

        $exceptions->render(function (QueryException $e) {
            $sqlState = $e->getCode();
            $driverCode = $e->errorInfo[1];
            $detail = $e->errorInfo[2] ?? '';

            $messages = [
                1451 => 'Resource cannot be deleted because it is still referenced by another resource.',
                1062 => 'Duplicate entry. This value already exists.',
                1048 => 'A required field is missing (cannot be null).',
            ];

            if ($sqlState === '23000' && isset($messages[$driverCode])) {
                return ApiResponse::badRequest(
                    $messages[$driverCode],
                    [
                        'code' => $driverCode,
                        'detail' => $detail,
                    ]
                );
            }

            return ApiResponse::badRequest(
                'Database error occurred.',
                [
                    'sqlState' => $sqlState,
                    'code' => $driverCode,
                    'detail' => $detail,
                ]
            );
        });
    })->create();
