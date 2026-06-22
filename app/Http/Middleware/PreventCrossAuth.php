<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreventCrossAuth
{
    /**
     * Redirect already-authenticated users to their correct dashboard.
     *
     * - Admin-authenticated users visiting /staff/login → redirect to admin dashboard
     * - Staff-authenticated users visiting /login → redirect to POS dashboard
     */
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();

        // Admin user trying to access staff login
        if ($path === 'staff/login' && Auth::check()) {
            return redirect()->route(Auth::user()->dashboardRouteName());
        }

        // Staff user trying to access admin login
        if ($path === 'login' && $request->session()->has('staff_token')) {
            return redirect()->route('pos.dashboard');
        }

        return $next($request);
    }
}
