<?php

namespace App\Listeners;

use App\Events\PasskeyLockout;
use App\Models\AuditLog;

class NotifyAdminOfLockout
{
    public function handle(PasskeyLockout $event): void
    {
        AuditLog::log('passkey_lockout_alert', $event->user->id, [
            'locked_user' => $event->user->name,
            'ip' => request()->ip(),
        ]);
    }
}
