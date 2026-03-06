<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * TokenFromQuery — DISABLED FOR SECURITY
 *
 * This middleware previously allowed token authentication via query string (?auth_token=xxx).
 * This is a security vulnerability: tokens in query strings are visible in server logs,
 * browser history, and proxy caches.
 *
 * Authentication must use Authorization: Bearer header or HttpOnly Cookie only.
 *
 * DO NOT re-enable without a full security review.
 */
class TokenFromQuery
{
    public function handle(Request $request, Closure $next): Response
    {
        // SECURITY: Token via query string is disabled.
        // Tokens in URLs appear in server logs, browser history, and proxy cache logs.
        // Use: Authorization: Bearer <token> header instead.

        return $next($request);
    }
}
