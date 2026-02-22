<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class InternalUsageRequest extends Model
{
    use HasUuid;

    protected $fillable = [
        'department', 'product_id', 'quantity', 'status', 'reason',
        'requested_by', 'approved_by', 'fulfilled_by',
        'rejected_reason', 'approved_at', 'fulfilled_at',
    ];

    protected $casts = [
        'quantity'     => 'decimal:3',
        'approved_at'  => 'datetime',
        'fulfilled_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function fulfiller()
    {
        return $this->belongsTo(User::class, 'fulfilled_by');
    }
}
