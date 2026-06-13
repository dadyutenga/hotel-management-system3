<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockReceivingItem extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'receiving_id', 'beverage_id', 'barcode_scanned',
        'quantity', 'unit_buying_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_buying_price' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    public function receiving(): BelongsTo
    {
        return $this->belongsTo(StockReceiving::class, 'receiving_id');
    }

    public function beverage(): BelongsTo
    {
        return $this->belongsTo(Beverage::class);
    }

    public function getTotalCostAttribute(): float
    {
        return $this->quantity * (float) $this->unit_buying_price;
    }
}
