<?php
// app/Models/User.php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, HasUuid, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role_id', 'is_active'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean'
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function isAdmin(): bool
    {
        return $this->role && $this->role->name === Role::ADMIN;
    }

    public function isSupervisor(): bool
    {
        return $this->role && $this->role->name === Role::SUPERVISOR;
    }

    public function isFrontDesk(): bool
    {
        return $this->role && $this->role->name === Role::FRONT_DESK;
    }

    public function isHouseHelp(): bool
    {
        return $this->role && $this->role->name === Role::HOUSE_HELP;
    }

    public function laundryTasks(): HasMany
    {
        return $this->hasMany(LaundryTask::class, 'assigned_to');
    }

    public function createdLaundryTasks(): HasMany
    {
        return $this->hasMany(LaundryTask::class, 'created_by');
    }
}