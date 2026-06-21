<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Role;
use App\Models\User;
use App\Services\AuthMethodResolver;
use App\Services\BuildingModuleGate;
use App\Support\PhoneNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('role')
            ->forUserBuilding()
            ->latest()
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereNotIn('name', [Role::ADMIN, Role::CASHIER])->get();
        $buildings = Building::active()->orderBy('name')->get();
        $roleAuthMethods = $roles->mapWithKeys(fn ($role) => [
            $role->id => AuthMethodResolver::forRole($role->name),
        ]);

        return view('users.create', compact('roles', 'buildings', 'roleAuthMethods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users|max:255',
            'phone' => 'required|string|max:30|unique:users,phone',
            'password' => 'nullable|string',
            'role_id' => [
                'required',
                'uuid',
                Rule::exists('roles', 'id')->whereNotIn('name', [Role::ADMIN]),
            ],
            'passkey' => 'nullable|string|max:4',
            'building_id' => 'required|uuid|exists:buildings,id',
            'is_active' => 'boolean',
        ]);

        $validated['phone'] = PhoneNumber::normalize($validated['phone']);

        if (! PhoneNumber::isValid($validated['phone'])) {
            return back()->withErrors(['phone' => __('auth.reset.invalid_phone')])->withInput();
        }

        // Determine auth method from role
        $role = Role::find($validated['role_id']);
        $isStaffRole = $role && AuthMethodResolver::forRole($role->name) === 'staff';

        if ($isStaffRole) {
            // Staff user: passkey required, no password needed
            if (empty($validated['passkey'])) {
                return back()->withErrors(['passkey' => 'A 4-digit PIN is required for staff users.'])->withInput();
            }
            // Manual confirmation check
            if ($validated['passkey'] !== ($request->passkey_confirmation ?? '')) {
                return back()->withErrors(['passkey' => 'The passkey confirmation does not match.'])->withInput();
            }
            if (! preg_match('/^\d{4}$/', $validated['passkey'])) {
                return back()->withErrors(['passkey' => 'The passkey must be exactly 4 digits.'])->withInput();
            }
            // Staff users don't use password login — set an unusable placeholder
            $validated['password'] = Hash::make(Str::random(40));
        } else {
            // Management user: password required
            if (empty($validated['password'])) {
                return back()->withErrors(['password' => 'A password is required for management users.'])->withInput();
            }
            $request->validate([
                'password' => [
                    'required',
                    'confirmed',
                    Password::min(10)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols(),
                ],
            ]);
            $validated['password'] = Hash::make($validated['password']);
        }

        if (! empty($validated['passkey'])) {
            $validated['passkey'] = Hash::make($validated['passkey']);
            $validated['passkey_enabled'] = true;
        } else {
            unset($validated['passkey']);
        }

        if ($role && ! $this->roleMatchesBuildingModules($role->name, $validated['building_id'] ?? null)) {
            return back()->withErrors(['building_id' => 'The selected building does not have the required module active for this role.'])->withInput();
        }

        $user = new User;
        $user->fill($validated);
        $user->role_id = $validated['role_id'];  // Explicitly set (not mass-assignable)
        $user->is_active = $validated['is_active'] ?? true;  // Explicitly set
        $user->building_id = $validated['building_id'];

        // Passkey fields are not in $fillable — set explicitly
        if (! empty($validated['passkey'])) {
            $user->passkey = $validated['passkey'];
            $user->passkey_enabled = true;
        }

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
        $roles = Role::whereNotIn('name', [Role::ADMIN, Role::CASHIER])->get();
        $buildings = Building::active()->orderBy('name')->get();
        $authMethod = AuthMethodResolver::forRole($user->roleName() ?? '');

        return view('users.edit', compact('user', 'roles', 'buildings', 'authMethod'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id.'|max:255',
            'phone' => 'required|string|max:30|unique:users,phone,'.$user->id,
            'password' => 'nullable|string',
            'role_id' => [
                'required',
                'uuid',
                Rule::exists('roles', 'id')->whereNotIn('name', [Role::ADMIN]),
            ],
            'passkey' => 'nullable|string|max:4',
            'building_id' => 'required|uuid|exists:buildings,id',
            'is_active' => 'boolean',
        ]);

        $validated['phone'] = PhoneNumber::normalize($validated['phone']);

        if (! PhoneNumber::isValid($validated['phone'])) {
            return back()->withErrors(['phone' => __('auth.reset.invalid_phone')])->withInput();
        }

        $previousPhone = $user->phone;

        if (! empty($validated['password'])) {
            $request->validate([
                'password' => [
                    'confirmed',
                    Password::min(10)
                        ->letters()
                        ->mixedCase()
                        ->numbers()
                        ->symbols(),
                ],
            ]);
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (! empty($validated['passkey'])) {
            // Manual confirmation check (avoids confirmed rule issues with autofill)
            if ($validated['passkey'] !== ($request->passkey_confirmation ?? '')) {
                return back()->withErrors(['passkey' => 'The passkey confirmation does not match.'])->withInput();
            }
            if (! preg_match('/^\d{4}$/', $validated['passkey'])) {
                return back()->withErrors(['passkey' => 'The passkey must be exactly 4 digits.'])->withInput();
            }
            $validated['passkey'] = Hash::make($validated['passkey']);
            $validated['passkey_enabled'] = true;
        } else {
            unset($validated['passkey']);
        }

        $role = Role::find($validated['role_id']);
        if ($role && ! $this->roleMatchesBuildingModules($role->name, $validated['building_id'] ?? null)) {
            return back()->withErrors(['building_id' => 'The selected building does not have the required module active for this role.'])->withInput();
        }

        // Separate guarded fields from mass-assignable fields
        $roleId = $validated['role_id'];
        $isActive = $validated['is_active'] ?? $user->is_active;
        $buildingId = $validated['building_id'];
        unset($validated['role_id'], $validated['is_active'], $validated['building_id']);

        $user->fill($validated);
        $user->role_id = $roleId;  // Explicitly set
        $user->is_active = $isActive;  // Explicitly set
        $user->building_id = $buildingId;

        // Passkey fields are not in $fillable — set explicitly
        if (! empty($validated['passkey'])) {
            $user->passkey = $validated['passkey'];
            $user->passkey_enabled = true;
        }

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

    protected function roleMatchesBuildingModules(string $roleName, ?string $buildingId): bool
    {
        if (! $buildingId) {
            return true;
        }

        if (in_array(strtolower($roleName), [Role::WAITER, Role::RESTAURANT_MANAGER, Role::CASHIER, Role::POS_KITCHEN], true)) {
            return BuildingModuleGate::hasRestaurant($buildingId);
        }

        if (in_array(strtolower($roleName), [Role::BAR_TENDER, Role::POS_BAR], true)) {
            return BuildingModuleGate::hasBar($buildingId);
        }

        return true;
    }
}
