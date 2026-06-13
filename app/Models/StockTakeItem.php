<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTakeItem extends Model
{
    use HasUuid, HasSoftDelete;

    protected $fillable = [
        'stock_take_id', 'beverage_id', 'barcode_scanned',
        'expected_quantity', 'physical_count', 'variance',
    ];

    protected $casts = [
        'expected_quantity' => 'integer',
        'physical_count' => 'integer',
        'variance' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function stockTake(): BelongsTo
    {
        return $this->belongsTo(StockTake::class, 'stock_take_id');
    }

    public function beverage(): BelongsTo
    {
        return $this->belongsTo(Beverage::class);
    }
}
