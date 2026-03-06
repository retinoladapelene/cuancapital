<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the authenticated user from either the standard session guard
 * OR the Sanctum API guard — whichever is active.
 *
 * This removes the duplicate:
 *   $user = Auth::user();
 *   if (!$user && Auth::guard('sanctum')->check()) {
 *       $user = Auth::guard('sanctum')->user();
 *   }
 * pattern found in 8+ controllers.
 *
 * After this middleware runs, $request->user() will return the resolved user
 * regardless of which guard authenticated them.
 */
class ResolveAuthUser
{
    public function handle(Request $request, Closure $next): Response
    {
        // If already authenticated via session, nothing to do
        if (! Auth::check()) {
            // Try Sanctum token guard
            if (Auth::guard('sanctum')->check()) {
                $user = Auth::guard('sanctum')->user();
                // Set the user on the default guard so $request->user() works everywhere
                Auth::setUser($user);
            }
        }

        return $next($request);
    }
}
