<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasskeyAuthService;
use Illuminate\Http\Request;

class StaffPasskeyLoginController extends Controller
{
    public function __construct(
        protected PasskeyAuthService $authService,
    ) {}

    public function showLoginForm()
    {
        $staff = User::where('passkey_enabled', true)
            ->where('is_active', true)
            ->whereHas('role', fn ($q) => $q->whereIn('name', ['waiter', 'cashier', 'bar_tender']))
            ->with('role')
            ->orderBy('name')
            ->get();

        return view('pos.login', compact('staff'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
            'passkey' => 'required|string|digits:4|numeric',
        ]);

        $user = User::find($request->user_id);

        if (! $user || ! $user->is_active) {
            return back()->withErrors(['user_id' => 'This account is not active.']);
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

        $request->session()->put('staff_token', $session->session_token);
        $request->session()->put('staff_user_id', $user->id);

        // Cashiers land on the settlement screen; waiters/bartenders land on order entry.
        if ($user->isCashier()) {
            return redirect()->route('pos.cashier.index');
        }

        return redirect()->route('pos.orders.create');
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
