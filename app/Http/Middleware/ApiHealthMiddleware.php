<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiHealthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Start time
        $startTime = microtime(true);

        // Process request
        $response = $next($request);

        // Calculate latency in milliseconds
        $latencyMs = round((microtime(true) - $startTime) * 1000);

        try {
            // Log to database asynchronously if possible, or synchronously
            \App\Models\ApiLog::create([
                'method' => $request->method(),
                'endpoint' => $request->path(),
                'status_code' => $response->getStatusCode(),
                'response_time_ms' => $latencyMs,
                'user_id' => auth()->id(), // null if not authenticated
            ]);
        } catch (\Exception $e) {
            // Fail silently so logging failures don't crash the API
            \Log::error('Failed to write ApiLog: ' . $e->getMessage());
        }

        return $response;
    }
}
