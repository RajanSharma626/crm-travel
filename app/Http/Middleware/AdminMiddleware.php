<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && (Auth::user()->hasRole('Admin') || Auth::user()->department === 'Admin')) {
            // User model now handles this
            return $next($request);
        }

        abort(403, 'Access denied. Admins only.');
    }
}
