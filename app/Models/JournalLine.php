<?php

namespace App\Models;

use App\Traits\HasSoftDelete;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class JournalLine extends Model
{
    use HasSoftDelete, HasUuids;

    protected $fillable = [
        'journal_entry_id', 'account_id', 'type', 'amount', 'notes',
    ];

    protected $casts = ['amount' => 'decimal:2', 'deleted_at' => 'datetime'];

    public function entry()
    {
        return $this->belongsTo(JournalEntry::class, 'journal_entry_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
