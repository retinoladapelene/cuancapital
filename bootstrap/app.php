<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Support\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->validateCsrfTokens(except: [
            'reverse-planner/calculate',
        ]);

        // Global middleware stack (runs on all requests)
        $middleware->append(\App\Http\Middleware\RequestTimingMiddleware::class);
        $middleware->append(\App\Http\Middleware\ApiLoggingMiddleware::class);

        // Middleware resolved per-route via alias
        $middleware->alias([
            'token.query'    => \App\Http\Middleware\TokenFromQuery::class,
            'resolve.auth'   => \App\Http\Middleware\ResolveAuthUser::class,
            'admin'          => \App\Http\Middleware\CheckAdmin::class,
            'maintenance'    => \App\Http\Middleware\CheckMaintenanceMode::class,
        ]);

        // Apply ResolveAuthUser to all API routes automatically
        $middleware->appendToGroup('api', \App\Http\Middleware\ResolveAuthUser::class);
        $middleware->appendToGroup('api', \App\Http\Middleware\ApiHealthMiddleware::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Standardized JSON error responses for all API requests
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::unauthorized('Silakan login untuk melanjutkan.');
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::error(
                    'Data yang dikirim tidak valid.',
                    422,
                    $e->errors()
                );
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::notFound('Resource yang diminta tidak ditemukan.');
            }
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return ApiResponse::error($e->getMessage() ?: 'HTTP Error', $e->getStatusCode());
            }
        });

        $exceptions->render(function (\Throwable $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $message = app()->isProduction()
                    ? 'Terjadi kesalahan server. Tim kami sudah diberitahu.'
                    : $e->getMessage();
                return ApiResponse::error($message, 500);
            }
        });
    })->create();

