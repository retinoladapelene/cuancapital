<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class RequestTimingMiddleware
{
    /**
     * Requests exceeding this threshold (ms) are flagged as WARN.
     */
    private const SLOW_THRESHOLD_MS = 300;

    public function handle(Request $request, Closure $next): Response
    {
        $requestId = (string) Str::uuid();
        $startTime = microtime(true);

        // Attach request ID to the request so other parts of the app can reference it
        $request->headers->set('X-Request-ID', $requestId);

        /** @var Response $response */
        $response = $next($request);

        $durationMs = (int) round((microtime(true) - $startTime) * 1000);

        $routeName  = $request->route()?->getName() ?? $request->path();
        $userId     = $request->user()?->id ?? 'guest';
        $method     = $request->method();

        $level   = $durationMs >= self::SLOW_THRESHOLD_MS ? 'warning' : 'info';
        $label   = $durationMs >= self::SLOW_THRESHOLD_MS ? '[WARN]' : '';

        Log::$level("[REQUEST TIMING]{$label} {$durationMs}ms method={$method} route={$routeName} user={$userId} request_id={$requestId}");

        // Expose request ID in response header for client-side tracing
        $response->headers->set('X-Request-ID', $requestId);

        return $response;
    }
}
