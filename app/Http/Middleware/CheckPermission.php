<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        if(!Auth::user()->hasRole('Root')){
            if (!Auth::user()->can($permission)) {
                // Redirige ou renvoie une réponse d'erreur
                abort(403, 'Unauthorized action.');
            }
        }

        return $next($request);
    }
}
