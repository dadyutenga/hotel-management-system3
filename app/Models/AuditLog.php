<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasUuid;

    protected $fillable = [
        'event_type', 'user_id', 'performed_by', 'ip_address', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public static function log(string $eventType, ?string $userId = null, array $metadata = [], ?string $performedBy = null): self
    {
        return static::create([
            'event_type' => $eventType,
            'user_id' => $userId,
            'performed_by' => $performedBy ?? auth()->id(),
            'ip_address' => request()->ip(),
            'metadata' => $metadata,
        ]);
    }
}
