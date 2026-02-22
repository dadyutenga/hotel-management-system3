<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

class StockLocation extends Model
{
    use HasUuid;

    protected $fillable = ['name', 'code', 'description', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public static function mainStore(): self
    {
        return static::where('code', 'main_store')->firstOrFail();
    }

    public static function bar(): self
    {
        return static::where('code', 'bar')->firstOrFail();
    }

    public static function kitchen(): self
    {
        return static::where('code', 'kitchen')->firstOrFail();
    }

    public function stockLevels()
    {
        return $this->hasMany(StockLevel::class, 'location_id');
    }
}
