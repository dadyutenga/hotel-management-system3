<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarDamageReport extends Model
{
    use HasUuid;

    protected $fillable = [
        'product_id',
        'location_id',
        'quantity',
        'reason',
        'notes',
        'reported_by',
        'reported_at',
        'stock_movement_id',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'reported_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(StockLocation::class, 'location_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function stockMovement(): BelongsTo
    {
        return $this->belongsTo(StockMovement::class, 'stock_movement_id');
    }
}
