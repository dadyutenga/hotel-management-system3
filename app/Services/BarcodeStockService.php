<?php

namespace App\Services;

use App\Events\StockReceived;
use App\Models\Beverage;
use App\Models\BeverageInventory;
use App\Models\BeverageStockMovement;
use App\Models\StockReceiving;
use App\Models\StockReceivingItem;
use App\Models\StockTake;
use App\Models\StockTakeItem;
use Illuminate\Support\Facades\DB;

class BarcodeStockService
{
    public function resolveBarcode(string $barcode): ?Beverage
    {
        return Beverage::byBarcode(trim($barcode))->active()->first();
    }

    public function createReceiving(string $userId, ?string $supplierId = null, ?string $notes = null): StockReceiving
    {
        return StockReceiving::create([
            'receiving_code' => StockReceiving::generateCode(),
            'received_by' => $userId,
            'supplier_id' => $supplierId,
            'notes' => $notes,
            'received_at' => now(),
        ]);
    }

    public function addScanToReceiving(StockReceiving $receiving, string $barcode, ?float $buyingPrice = null): StockReceivingItem
    {
        $beverage = $this->resolveBarcode($barcode);

        if (! $beverage) {
            throw new \RuntimeException("Beverage not found for barcode: {$barcode}");
        }

        $existingItem = StockReceivingItem::where('receiving_id', $receiving->id)
            ->where('beverage_id', $beverage->id)
            ->first();

        if ($existingItem) {
            $existingItem->increment('quantity', 1);

            return $existingItem->refresh();
        }

        return StockReceivingItem::create([
            'receiving_id' => $receiving->id,
            'beverage_id' => $beverage->id,
            'barcode_scanned' => trim($barcode),
            'quantity' => 1,
            'unit_buying_price' => $buyingPrice ?? $beverage->buying_price,
        ]);
    }

    public function updateReceivingItemQuantity(StockReceivingItem $item, int $quantity): void
    {
        $item->update(['quantity' => max(1, $quantity)]);
    }

    public function confirmReceiving(StockReceiving $receiving, string $userId): void
    {
        if ($receiving->status === 'completed') {
            throw new \RuntimeException('This receiving has already been confirmed.');
        }

        DB::transaction(function () use ($receiving, $userId) {
            // Reload under row lock to prevent concurrent confirmations
            $receiving = StockReceiving::lockForUpdate()->findOrFail($receiving->id);
            if ($receiving->status === 'completed') {
                throw new \RuntimeException('This receiving has already been confirmed.');
            }

            $receiving->items->each(function (StockReceivingItem $item) use ($userId) {
                $inventory = BeverageInventory::firstOrCreate(
                    ['beverage_id' => $item->beverage_id],
                    ['quantity_on_hand' => 0]
                );

                $inventory = BeverageInventory::where('id', $inventory->id)->lockForUpdate()->first();
                $inventory->increment('quantity_on_hand', $item->quantity);
                $inventory->update(['last_updated' => now()]);

                BeverageStockMovement::create([
                    'beverage_id' => $item->beverage_id,
                    'movement_type' => 'IN',
                    'quantity' => $item->quantity,
                    'reference_type' => 'stock_receiving',
                    'reference_id' => $receiving->id,
                    'notes' => "Received via {$receiving->receiving_code}",
                    'performed_by' => $userId,
                ]);
            });

            $receiving->update(['status' => 'completed']);

            event(new StockReceived($receiving));
        });
    }

    public function createStockTake(string $userId, ?string $notes = null): StockTake
    {
        return StockTake::create([
            'stock_take_code' => StockTake::generateCode(),
            'initiated_by' => $userId,
            'status' => 'in_progress',
            'notes' => $notes,
            'started_at' => now(),
        ]);
    }

    public function addScanToStockTake(StockTake $stockTake, string $barcode): StockTakeItem
    {
        $beverage = $this->resolveBarcode($barcode);

        if (! $beverage) {
            throw new \RuntimeException("Beverage not found for barcode: {$barcode}");
        }

        $existingItem = StockTakeItem::where('stock_take_id', $stockTake->id)
            ->where('beverage_id', $beverage->id)
            ->first();

        if ($existingItem) {
            $existingItem->increment('physical_count', 1);
            $existingItem->update([
                'variance' => $existingItem->physical_count - $existingItem->expected_quantity,
            ]);

            return $existingItem->refresh();
        }

        $expectedQty = BeverageInventory::where('beverage_id', $beverage->id)->value('quantity_on_hand') ?? 0;

        return StockTakeItem::create([
            'stock_take_id' => $stockTake->id,
            'beverage_id' => $beverage->id,
            'barcode_scanned' => trim($barcode),
            'expected_quantity' => $expectedQty,
            'physical_count' => 1,
            'variance' => 1 - $expectedQty,
        ]);
    }

    public function completeStockTake(StockTake $stockTake, string $userId): void
    {
        if ($stockTake->status === 'completed') {
            throw new \RuntimeException('This stock-take has already been completed.');
        }

        DB::transaction(function () use ($stockTake, $userId) {
            $stockTake->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $stockTake->items->each(function (StockTakeItem $item) use ($userId) {
                if ($item->variance === 0) {
                    return;
                }

                $inventory = BeverageInventory::firstOrCreate(
                    ['beverage_id' => $item->beverage_id],
                    ['quantity_on_hand' => 0]
                );

                $inventory = BeverageInventory::where('id', $inventory->id)->lockForUpdate()->first();
                $oldQty = $inventory->quantity_on_hand;
                $inventory->update([
                    'quantity_on_hand' => $item->physical_count,
                    'last_updated' => now(),
                ]);

                BeverageStockMovement::create([
                    'beverage_id' => $item->beverage_id,
                    'movement_type' => 'ADJUSTMENT',
                    'quantity' => $item->physical_count - $oldQty,
                    'reference_type' => 'stock_take',
                    'reference_id' => $stockTake->id,
                    'notes' => "Stock-take {$stockTake->stock_take_code}: expected {$item->expected_quantity}, counted {$item->physical_count}",
                    'performed_by' => $userId,
                ]);
            });
        });
    }
}
