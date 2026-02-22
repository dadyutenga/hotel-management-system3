<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasUuid;

    protected $fillable = ['location_id', 'table_number', 'capacity', 'status', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function location()    { return $this->belongsTo(StockLocation::class, 'location_id'); }
    public function activeOrder() { return $this->hasOne(Order::class)->whereNotIn('status', ['settled', 'cancelled']); }
}
