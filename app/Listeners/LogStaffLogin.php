<?php

namespace App\Listeners;

use App\Events\StaffLoggedIn;
use App\Models\AuditLog;

class LogStaffLogin
{
    public function handle(StaffLoggedIn $event): void
    {
        AuditLog::log('staff_logged_in', $event->user->id, [
            'ip' => request()->ip(),
        ], $event->user->id);
    }
}
