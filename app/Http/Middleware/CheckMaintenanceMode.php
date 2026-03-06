<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SystemSetting;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Try to fetch the setting
        try {
            $isMaintenance = SystemSetting::getSetting('system_maintenance', '0') === '1';
        } catch (\Exception $e) {
            // Failsafe: if database is down or table is missing, don't crash the whole app.
            $isMaintenance = false;
        }

        if ($isMaintenance) {
            // Allow Admins to bypass maintenance mode
            if (auth()->check() && auth()->user()->role === 'admin') {
                return $next($request);
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'System under maintenance.'
                ], 503);
            }
            // Return to a custom maintenance view if needed, or simple status code
            abort(503, 'CuanCapital is currently undergoing scheduled maintenance. Please check back later.');
        }

        return $next($request);
    }
}
