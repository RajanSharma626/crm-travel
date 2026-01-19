<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     */
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        $employee = Auth::user();

        // Admin has all permissions
        if ($employee->hasRole('Admin') || $employee->hasRole('Developer') || $employee->department === 'Admin') {
            return $next($request);
        }

        // Check if employee has any of the required permissions
        foreach ($permissions as $permission) {
            if ($employee->can($permission)) {
                return $next($request);
            }
        }

        abort(403, 'You do not have permission to access this resource.');
    }
}

