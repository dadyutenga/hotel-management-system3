<?php

namespace App\Models;

use App\Traits\BuildingScoped;
use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockReceiving extends Model
{
    use BuildingScoped, HasSoftDelete, HasUuid;

    protected $fillable = [
        'receiving_code', 'status', 'received_by', 'supplier_id', 'notes', 'received_at', 'building_id',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockReceivingItem::class, 'receiving_id');
    }

    public static function generateCode(): string
    {
        $date = now()->format('Ymd');
        $count = static::whereDate('created_at', today())->count() + 1;

        return "RCV-{$date}-".str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
