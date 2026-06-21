<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuthMethodResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('These credentials do not match our records.'),
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => __('Your account has been deactivated.'),
            ]);
        }

        if (! $user->isAdmin() && ! $user->building_id) {
            throw ValidationException::withMessages([
                'email' => __('Your account is not assigned to a property. Please contact an administrator.'),
            ]);
        }

        if (! AuthMethodResolver::canUseAdminLogin($user)) {
            throw ValidationException::withMessages([
                'email' => __('This account is for staff PIN login only. Use the staff login instead.'),
            ]);
        }

        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        if ($user->must_change_password) {
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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $response = redirect()->route('login');

        $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');

        return $response;
    }
}
