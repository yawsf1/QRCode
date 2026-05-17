<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Standardize current user role to lowercase
        $userRole = trim(strtolower($request->user()->role));

        // Standardize allowed roles input collection
        $allowedRoles = array_map(function($role) {
            return trim(strtolower($role));
        }, $roles);

        // If validation fails, log it for analysis before throwing the 403
        if (!in_array($userRole, $allowedRoles)) {
            Log::warning("Access Denied: User ID {$request->user()->id} tried to access a route requiring " . implode(' or ', $allowedRoles) . ". User has role: '{$userRole}'.");
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}