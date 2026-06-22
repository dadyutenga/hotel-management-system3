<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthMethodResolver;
use App\Services\PasskeyAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffPasskeyLoginController extends Controller
{
    public function __construct(
        protected PasskeyAuthService $authService,
    ) {}

    public function showLoginForm()
    {
        return view('pos.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'passkey' => 'required|string|digits:4|numeric',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'No account found with that email.'])->withInput();
        }

        if (! $user->is_active) {
            return back()->withErrors(['email' => 'This account has been deactivated.'])->withInput();
        }

        if (! $user->passkey_enabled) {
            return back()->withErrors(['email' => 'Passkey login is not enabled for this account.'])->withInput();
        }

        if (! AuthMethodResolver::canUseStaffLogin($user)) {
            return back()->withErrors(['email' => 'Admin and managers must use the management login.'])->withInput();
        }

        $result = $this->authService->verifyPasskey($user, $request->passkey);

        if (! $result['success']) {
            if ($result['locked'] ?? false) {
                return back()->withErrors([
                    'passkey' => $result['message'],
                ])->with('locked_until', $result['locked_until'] ?? null);
            }

            return back()->withErrors([
                'passkey' => $result['message'],
            ])->with('attempts_remaining', $result['attempts_remaining'] ?? null);
        }

        $session = $result['session'];

        // For POS roles, use staff session.
        // For other roles (supervisor, front_desk, etc.), use standard Auth login.
        if ($user->hasAnyRole(['waiter', 'bar_tender', 'pos_bar', 'pos_kitchen'])) {
            $request->session()->put('staff_token', $session->session_token);
            $request->session()->put('staff_user_id', $user->id);
            $request->session()->regenerate();

            // Settlement roles go to cashier screen
            if ($user->isPosBar() || $user->isPosKitchen()) {
                return redirect()->route('pos.cashier.index');
            }

            // Order entry roles go to order screen
            return redirect()->route('pos.orders.create');
        }

        // Non-POS roles: use standard auth so they access management routes
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->dashboardRouteName());
    }

    public function logout(Request $request)
    {
        $token = $request->session()->get('staff_token');
        $userId = $request->session()->get('staff_user_id');

        if ($token && $userId) {
            $this->authService->logoutSession($token, $userId);
        }

        $request->session()->forget(['staff_token', 'staff_user_id']);

        return redirect()->route('staff.login');
    }

    public function forceLogout(Request $request, User $user)
    {
        $cashierId = $request->attributes->get('staffUser')?->id ?? auth()->id();

        $activeSession = $user->activePasskeySession();

        if ($activeSession) {
            $this->authService->logoutSession(
                $activeSession->session_token,
                $user->id,
                $cashierId,
            );
        }

        return back()->with('success', "{$user->name} has been logged out.");
    }
}
