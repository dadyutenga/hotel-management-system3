<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\PasskeySession;
use App\Models\User;
use App\Events\StaffLoggedIn;
use App\Events\StaffLoggedOut;
use App\Events\PasskeyLockout;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasskeyAuthService
{
    public function verifyPasskey(User $user, string $passkey): array
    {
        if (!$user->passkey_enabled) {
            return ['success' => false, 'message' => 'Passkey login is not enabled for this user.'];
        }

        if ($user->isPasskeyLocked()) {
            $minutes = now()->diffInMinutes($user->passkey_locked_until, false);
            return [
                'success' => false,
                'locked' => true,
                'message' => "Account locked. Try again in {$minutes} minutes.",
                'locked_until' => $user->passkey_locked_until,
            ];
        }

        if (!Hash::check($passkey, $user->passkey)) {
            return $this->handleFailedAttempt($user);
        }

        $user->failed_passkey_attempts = 0;
        $user->passkey_locked_until = null;
        $user->last_passkey_login = now();
        $user->save();

        $session = $this->createSession($user);

        event(new StaffLoggedIn($user));

        AuditLog::log('passkey_login_success', $user->id, [
            'ip' => request()->ip(),
        ], $user->id);

        return ['success' => true, 'session' => $session];
    }

    protected function handleFailedAttempt(User $user): array
    {
        $maxAttempts = config('hms_auth.passkey.max_attempts', 5);
        $lockoutMinutes = config('hms_auth.passkey.lockout_minutes', 15);

        $newAttempts = $user->failed_passkey_attempts + 1;

        if ($newAttempts >= $maxAttempts) {
            $user->passkey_locked_until = now()->addMinutes($lockoutMinutes);
            $user->failed_passkey_attempts = 0;
            $user->save();

            event(new PasskeyLockout($user));

            AuditLog::log('passkey_lockout', $user->id, [
                'ip' => request()->ip(),
                'attempts' => $newAttempts,
            ]);

            return [
                'success' => false,
                'locked' => true,
                'message' => "Account locked after {$maxAttempts} failed attempts. Try again in {$lockoutMinutes} minutes.",
                'locked_until' => $user->passkey_locked_until,
            ];
        }

        $user->failed_passkey_attempts = $newAttempts;
        $user->save();

        $remaining = $maxAttempts - $newAttempts;

        AuditLog::log('passkey_login_failed', $user->id, [
            'ip' => request()->ip(),
            'attempt' => $newAttempts,
        ]);

        return [
            'success' => false,
            'message' => "Incorrect PIN. {$remaining} attempts remaining before lockout.",
            'attempts_remaining' => $remaining,
        ];
    }

    protected function createSession(User $user): PasskeySession
    {
        $hours = config('hms_auth.staff_session.hours', 8);

        return PasskeySession::create([
            'user_id' => $user->id,
            'session_token' => Str::random(64),
            'logged_in_at' => now(),
            'ip_address' => request()->ip(),
            'session_expires_at' => now()->addHours($hours),
        ]);
    }

    public function logoutSession(string $token, string $userId, ?string $closedByUserId = null): void
    {
        $session = PasskeySession::where('session_token', $token)
            ->where('user_id', $userId)
            ->whereNull('logged_out_at')
            ->first();

        if ($session) {
            $session->update([
                'logged_out_at' => now(),
                'logged_out_by' => $closedByUserId,
            ]);

            event(new StaffLoggedOut($session->user, $closedByUserId));

            AuditLog::log('passkey_logout', $userId, [
                'session_token' => substr($token, 0, 8) . '...',
                'closed_by' => $closedByUserId,
            ], $closedByUserId ?? $userId);
        }
    }

    public function validateSession(string $token, string $userId): ?PasskeySession
    {
        $session = PasskeySession::where('session_token', $token)
            ->where('user_id', $userId)
            ->whereNull('logged_out_at')
            ->first();

        if (!$session) {
            return null;
        }

        if ($session->session_expires_at && $session->session_expires_at->isPast()) {
            $session->update(['logged_out_at' => now()]);
            return null;
        }

        return $session;
    }

    public function unlockUser(User $user, ?string $adminId = null): void
    {
        $user->failed_passkey_attempts = 0;
        $user->passkey_locked_until = null;
        $user->save();

        AuditLog::log('passkey_unlock', $user->id, [], $adminId ?? auth()->id());
    }

    public function resetPasskey(User $user, string $newPin, ?string $adminId = null): void
    {
        $user->passkey = bcrypt($newPin);
        $user->passkey_enabled = true;
        $user->failed_passkey_attempts = 0;
        $user->passkey_locked_until = null;
        $user->save();

        AuditLog::log('passkey_reset', $user->id, [], $adminId ?? auth()->id());
    }
}
