<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminOrOperations
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Check if user is Admin or Operations
        $isAdmin = $user && (
            $user->hasRole('Admin') || 
            $user->hasRole('Developer') || 
            $user->department === 'Admin'
        );
        
        $isOperations = $user && (
            $user->department === 'Operation' || 
            $user->department === 'Operations' ||
            (method_exists($user, 'hasRole') && 
                ($user->hasRole('Operation') || $user->hasRole('Operations')))
        );
        
        if ($isAdmin || $isOperations) {
            return $next($request);
        }
        
        // Redirect unauthorized users
        abort(403, 'Access denied. This section is only accessible to Admin and Operations.');
    }
}
