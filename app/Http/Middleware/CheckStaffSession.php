<?php

namespace App\Http\Middleware;

use App\Models\PasskeySession;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class CheckStaffSession
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->session()->get('staff_token');
        $userId = $request->session()->get('staff_user_id');

        if (!$token || !$userId) {
            return redirect()->route('staff.login');
        }

        $session = PasskeySession::where('session_token', $token)
            ->where('user_id', $userId)
            ->whereNull('logged_out_at')
            ->first();

        if (!$session) {
            $request->session()->forget(['staff_token', 'staff_user_id']);
            return redirect()->route('staff.login');
        }

        if ($session->session_expires_at && $session->session_expires_at->isPast()) {
            $session->update(['logged_out_at' => now()]);
            $request->session()->forget(['staff_token', 'staff_user_id']);
            return redirect()->route('staff.login');
        }

        $request->attributes->set('staffUser', $session->user);
        $request->attributes->set('staffSession', $session);

        View::share('staffUser', $session->user);
        View::share('staffSession', $session);

        return $next($request);
    }
}
