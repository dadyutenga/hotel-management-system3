<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller {
    public function index() {
        $users = User::with('role')->latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create() {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => [
                'required',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(10)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'role_id' => 'required|uuid|exists:roles,id',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        $user = new User();
        $user->fill($validated);
        $user->role_id = $validated['role_id'];  // Explicitly set (not mass-assignable)
        $user->is_active = $validated['is_active'] ?? true;  // Explicitly set
        $user->save();
        
        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user) {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user) {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id.'|max:255',
            'password' => [
                'nullable',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(10)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'role_id' => 'required|uuid|exists:roles,id',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Separate guarded fields from mass-assignable fields
        $roleId = $validated['role_id'];
        $isActive = $validated['is_active'] ?? $user->is_active;
        unset($validated['role_id'], $validated['is_active']);

        $user->fill($validated);
        $user->role_id = $roleId;  // Explicitly set
        $user->is_active = $isActive;  // Explicitly set
        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user) {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}