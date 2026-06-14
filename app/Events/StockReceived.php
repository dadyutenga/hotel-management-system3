<?php

namespace App\Events;

use App\Models\StockReceiving;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(public StockReceiving $receiving) {}
}
