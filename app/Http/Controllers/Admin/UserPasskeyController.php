<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PasskeyAuthService;
use Illuminate\Http\Request;

class UserPasskeyController extends Controller
{
    public function __construct(
        protected PasskeyAuthService $authService,
    ) {}

    public function edit(User $user)
    {
        return view('admin.users.passkey', compact('user'));
    }

    public function resetPasskey(Request $request, User $user)
    {
        $request->validate([
            'passkey' => 'required|string|digits:4|numeric|confirmed',
        ]);

        $this->authService->resetPasskey($user, $request->passkey, auth()->id());

        return redirect()->route('users.index')
            ->with('success', "Passkey reset for {$user->name}.");
    }

    public function unlock(User $user)
    {
        $this->authService->unlockUser($user, auth()->id());

        return redirect()->route('users.index')
            ->with('success', "{$user->name} has been unlocked.");
    }

    public function togglePasskey(Request $request, User $user)
    {
        $request->validate([
            'passkey_enabled' => 'required|boolean',
        ]);

        $user->passkey_enabled = $request->boolean('passkey_enabled');
        $user->save();

        return back()->with('success', 'Passkey login '.($request->boolean('passkey_enabled') ? 'enabled' : 'disabled')." for {$user->name}.");
    }
}
