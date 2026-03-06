<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in
        if (!auth()->check()) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Unauthorized. Admin access required.'], 401);
            }
            return redirect('/login')->with('error', 'You must be logged in.');
        }

        // Check if user role is admin
        if (auth()->user()->role !== 'admin') {
             if ($request->wantsJson()) {
                 return response()->json([
                     'success' => false,
                     'message' => 'Forbidden. Admin privileges required.'
                 ], 403);
             }
             return redirect('/')->with('error', 'You do not have permission to access that area.');
        }

        return $next($request);
    }
}
