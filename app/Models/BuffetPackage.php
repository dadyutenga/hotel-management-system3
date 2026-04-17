<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BuffetPackage extends Model
{
    use HasUuid;

    protected $fillable = [
        'name',
        'adult_price',
        'child_price',
        'available_days',
        'start_time',
        'end_time',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'adult_price' => 'decimal:2',
        'child_price' => 'decimal:2',
        'available_days' => 'array',
        'is_active' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function buffetSales(): HasMany
    {
        return $this->hasMany(BuffetSale::class);
    }
}

