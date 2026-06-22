<?php

namespace App\Listeners;

use App\Events\StockReceived;

class UpdateLowStockAlerts
{
    public function handle(StockReceived $event): void {}
}
