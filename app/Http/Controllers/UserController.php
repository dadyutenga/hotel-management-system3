<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Support\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->latest()->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereNotIn('name', [Role::ADMIN])->get();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users|max:255',
            'phone' => 'required|string|max:30|unique:users,phone',
            'password' => [
                'required',
                'confirmed',
                Password::min(10)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'role_id' => [
                'required',
                'uuid',
                Rule::exists('roles', 'id')->whereNotIn('name', [Role::ADMIN]),
            ],
            'login_type' => 'required|in:full,staff,both',
            'property_code' => 'nullable|string|max:50',
            'passkey' => 'nullable|string|digits:4|numeric|confirmed',
            'is_active' => 'boolean',
        ]);

        $validated['phone'] = PhoneNumber::normalize($validated['phone']);

        if (! PhoneNumber::isValid($validated['phone'])) {
            return back()->withErrors(['phone' => __('auth.reset.invalid_phone')])->withInput();
        }

        $validated['password'] = Hash::make($validated['password']);

        if (! empty($validated['passkey'])) {
            $validated['passkey'] = Hash::make($validated['passkey']);
            $validated['passkey_enabled'] = true;
        } else {
            unset($validated['passkey']);
        }

        $user = new User;
        $user->fill($validated);
        $user->role_id = $validated['role_id'];  // Explicitly set (not mass-assignable)
        $user->is_active = $validated['is_active'] ?? true;  // Explicitly set
        $user->login_type = $validated['login_type'];
        $user->property_code = $validated['property_code'] ?? null;
        $user->save();

        Log::info('Admin created user with phone number.', [
            'actor_user_id' => auth()->id(),
            'created_user_id' => $user->id,
            'phone_hash' => hash('sha256', (string) $user->phone),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::whereNotIn('name', [Role::ADMIN])->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id.'|max:255',
            'phone' => 'required|string|max:30|unique:users,phone,'.$user->id,
            'password' => [
                'nullable',
                'confirmed',
                Password::min(10)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'role_id' => [
                'required',
                'uuid',
                Rule::exists('roles', 'id')->whereNotIn('name', [Role::ADMIN]),
            ],
            'login_type' => 'required|in:full,staff,both',
            'property_code' => 'nullable|string|max:50',
            'passkey' => 'nullable|string|digits:4|numeric|confirmed',
            'is_active' => 'boolean',
        ]);

        $validated['phone'] = PhoneNumber::normalize($validated['phone']);

        if (! PhoneNumber::isValid($validated['phone'])) {
            return back()->withErrors(['phone' => __('auth.reset.invalid_phone')])->withInput();
        }

        $previousPhone = $user->phone;

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (! empty($validated['passkey'])) {
            $validated['passkey'] = Hash::make($validated['passkey']);
            $validated['passkey_enabled'] = true;
        } else {
            unset($validated['passkey']);
        }

        // Separate guarded fields from mass-assignable fields
        $roleId = $validated['role_id'];
        $isActive = $validated['is_active'] ?? $user->is_active;
        $loginType = $validated['login_type'];
        $propertyCode = $validated['property_code'] ?? null;
        unset($validated['role_id'], $validated['is_active'], $validated['login_type'], $validated['property_code']);

        $user->fill($validated);
        $user->role_id = $roleId;  // Explicitly set
        $user->is_active = $isActive;  // Explicitly set
        $user->login_type = $loginType;
        $user->property_code = $propertyCode;
        $user->save();

        if ($previousPhone !== $user->phone) {
            Log::info('Admin updated user phone number.', [
                'actor_user_id' => auth()->id(),
                'target_user_id' => $user->id,
                'previous_phone_hash' => $previousPhone ? hash('sha256', $previousPhone) : null,
                'new_phone_hash' => $user->phone ? hash('sha256', $user->phone) : null,
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $this->softDelete($user);

        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }

    public function archived()
    {
        $users = User::onlyDeleted()->with('role')->latest('deleted_at')->paginate(20);

        return view('users.archived', compact('users'));
    }

    public function restore(User $user)
    {
        $this->restoreModel($user);

        return redirect()->route('users.index')->with('success', 'User restored successfully.');
    }
}
