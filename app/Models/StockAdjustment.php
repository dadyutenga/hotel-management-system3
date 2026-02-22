<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class StockAdjustment extends Model
{
    use HasUuid;

    protected $fillable = [
        'product_id', 'location_id', 'previous_qty', 'new_qty',
        'difference', 'reason', 'requires_approval', 'status', 'approved_by', 'created_by',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'previous_qty'      => 'decimal:3',
        'new_qty'           => 'decimal:3',
        'difference'        => 'decimal:3',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function location()
    {
        return $this->belongsTo(StockLocation::class, 'location_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
