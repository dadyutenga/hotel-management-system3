<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasskeySession extends Model
{
    use HasUuid;

    protected $fillable = [
        'user_id', 'session_token', 'logged_in_at',
        'logged_out_at', 'logged_out_by', 'ip_address', 'session_expires_at',
    ];

    protected $casts = [
        'logged_in_at' => 'datetime',
        'logged_out_at' => 'datetime',
        'session_expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_out_by');
    }

    public function isActive(): bool
    {
        return is_null($this->logged_out_at)
            && (is_null($this->session_expires_at) || $this->session_expires_at->isFuture());
    }

    public function scopeActive($query)
    {
        return $query->whereNull('logged_out_at')
            ->where(function ($q) {
                $q->whereNull('session_expires_at')
                    ->orWhere('session_expires_at', '>', now());
            });
    }
}
