<?php

namespace App\Listeners;

use App\Events\StaffLoggedOut;
use App\Models\AuditLog;

class LogStaffLogout
{
    public function handle(StaffLoggedOut $event): void
    {
        AuditLog::log('staff_logged_out', $event->user->id, [
            'closed_by' => $event->closedByUserId,
        ], $event->closedByUserId ?? $event->user->id);
    }
}
