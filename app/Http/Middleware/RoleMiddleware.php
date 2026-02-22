<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleMiddleware {
    public function handle(Request $request, Closure $next, ...$roles) {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Null-safe check: reject if user has no role assigned
        if (!$user->role) {
            Log::warning('User without role attempted access', ['user_id' => $user->id, 'path' => $request->path()]);
            abort(403, 'Unauthorized action. No role assigned.');
        }

        $userRole = $user->role->name;
        
        if (!in_array($userRole, $roles)) {
            Log::warning('Unauthorized role access attempt', [
                'user_id' => $user->id,
                'user_role' => $userRole,
                'required_roles' => $roles,
                'path' => $request->path(),
            ]);
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}