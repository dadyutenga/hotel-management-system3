<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class BuildingScopeMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if ($user && ! $user->isAdmin() && ! $user->building_id) {
            abort(403, 'Your account is not assigned to a building. Please contact an administrator.');
        }

        return $next($request);
    }
}
