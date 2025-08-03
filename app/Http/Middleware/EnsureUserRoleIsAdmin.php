<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRoleIsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role?->slug !== 'admin') {
            abort(403, 'Vous n\'avez pas les droits d\'administrateur nécessaires.');
        }

        return $next($request);
    }
}