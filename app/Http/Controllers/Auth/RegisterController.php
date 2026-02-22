<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller {
    public function showRegistrationForm() {
        return view('auth.register');
    }

    public function register(Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Get front_desk role as default for new registrations
        $defaultRole = Role::where('name', Role::FRONT_DESK)->first();

        $user = new User();
        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->role_id = $defaultRole->id;  // Explicitly set (not mass-assignable)
        $user->is_active = false;  // Require admin approval before account activation
        $user->save();

        // Do NOT auto-login — redirect to login with pending approval message
        return redirect()->route('login')->with('info', 'Your account has been created and is pending admin approval.');
    }
}