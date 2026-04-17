<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller {
    public function showLoginForm() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials, $request->filled('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        // Check if user is active
        if (!Auth::user()->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('Your account has been deactivated.'),
            ]);
        }

        $request->session()->regenerate();

        if (Auth::user()->must_change_password) {
            Log::info('User logged in with temporary password and must rotate password.', [
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
            ]);

            return redirect()
                ->route('profile.edit')
                ->with('info', __('auth.reset.must_change_password_notice'));
        }

        return redirect()->intended(route(Auth::user()->dashboardRouteName()));
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
