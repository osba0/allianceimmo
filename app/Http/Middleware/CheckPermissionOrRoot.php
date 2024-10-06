<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermissionOrRoot
{
    public function handle($request, Closure $next, $permission)
    {
        $user = Auth::user();

        if ($user->hasRole('root') || $user->can($permission)) {
            return $next($request);
        }

        // Si l'utilisateur n'a pas la permission ou le rôle root
        abort(403, 'Unauthorized action.');
    }
}
