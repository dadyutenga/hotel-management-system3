<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    use HasUuid;

    protected $fillable = [
        'from_location_id', 'to_location_id', 'product_id', 'quantity',
        'status', 'reason', 'requested_by', 'approved_by', 'approved_at',
        'rejected_by', 'rejected_at', 'rejection_reason', 'fulfilled_by', 'completed_at',
    ];

    protected $casts = [
        'quantity'     => 'decimal:3',
        'approved_at'  => 'datetime',
        'rejected_at'  => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function fromLocation()
    {
        return $this->belongsTo(StockLocation::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(StockLocation::class, 'to_location_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function fulfiller()
    {
        return $this->belongsTo(User::class, 'fulfilled_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}
