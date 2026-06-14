<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BeverageInventory extends Model
{
    use HasSoftDelete, HasUuid;

    public $timestamps = false;

    protected $table = 'beverage_inventory';

    protected $fillable = ['beverage_id', 'quantity_on_hand', 'last_updated'];

    protected $casts = [
        'quantity_on_hand' => 'integer',
        'last_updated' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function beverage(): BelongsTo
    {
        return $this->belongsTo(Beverage::class);
    }
}
