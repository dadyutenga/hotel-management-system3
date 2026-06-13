<?php

namespace App\Listeners;

use App\Events\StockReceived;
use App\Models\Beverage;
use App\Models\BeverageInventory;
use App\Models\StoreNotification;
use App\Models\User;

class UpdateLowStockAlerts
{
    public function handle(StockReceived $event): void
    {
    }
}
