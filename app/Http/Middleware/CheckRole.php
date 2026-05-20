<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }
        $userRole = trim(strtolower($request->user()->role));
        $allowedRoles = array_map(function($role) {
            return trim(strtolower($role));
        }, $roles);
        if (!in_array($userRole, $allowedRoles)) {
            Log::warning("Access Denied: User ID {$request->user()->id} tried to access a route requiring " . implode(' or ', $allowedRoles) . ". User has role: '{$userRole}'.");
            abort(403, 'Accès non autorisé.');
        }
        return $next($request);
    }
}