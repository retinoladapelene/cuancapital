<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApiLoggingMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $durationMs = ($endTime - $startTime) * 1000;
        
        // Define core paths we want to monitor
        $monitoredPrefixes = [
            'api',
            'reverse-planner',
            'profit-simulator',
            'mentor'
        ];
        
        if (Str::startsWith($request->path(), $monitoredPrefixes) && !$request->isMethod('OPTIONS')) {
            try {
                DB::table('api_logs')->insert([
                    'endpoint' => '/' . ltrim($request->path(), '/'),
                    'method' => $request->method(),
                    'user_id' => $request->user()?->id,
                    'status_code' => $response->getStatusCode(),
                    'response_time_ms' => $durationMs,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Fail silently: Do not break the primary request cycle if logging fails
            }
        }
        
        return $response;
    }
}
