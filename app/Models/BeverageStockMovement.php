<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeverageStockMovement extends Model
{
    use HasSoftDelete, HasUuid;

    protected $fillable = [
        'beverage_id', 'movement_type', 'quantity',
        'reference_type', 'reference_id', 'notes', 'performed_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function beverage(): BelongsTo
    {
        return $this->belongsTo(Beverage::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
