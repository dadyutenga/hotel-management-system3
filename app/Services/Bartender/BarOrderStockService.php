<?php

namespace App\Services\Bartender;

use App\Models\Order;
use App\Models\StockLevel;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class BarOrderStockService
{
    public function checkAvailability(Order $order): array
    {
        $order->loadMissing('items.menuItem.ingredients.product');

        $errors = [];

        foreach ($order->items as $orderItem) {
            if ($orderItem->status === 'cancelled') {
                continue;
            }

            $menuItemName = $orderItem->item_name_snapshot ?? $orderItem->menuItem->name;

            foreach ($orderItem->menuItem->ingredients as $ingredient) {
                $level = StockLevel::query()
                    ->where('product_id', $ingredient->product_id)
                    ->where('location_id', $order->location_id)
                    ->first();

                $required = (float) $ingredient->quantity * (int) $orderItem->quantity;
                $available = (float) ($level?->available_qty ?? 0);

                if ($available < $required) {
                    $errors[] = [
                        'menu_item' => $menuItemName,
                        'product' => $ingredient->product->name,
                        'required' => $required,
                        'available' => $available,
                        'message' => "Insufficient stock for {$ingredient->product->name} (required {$required}, available {$available}).",
                    ];
                }
            }
        }

        return [
            'ok' => count($errors) === 0,
            'errors' => $errors,
        ];
    }

    public function deductForOrder(Order $order, string $actorId): bool
    {
        return DB::transaction(function () use ($order, $actorId) {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($locked->stock_deducted_at) {
                return false;
            }

            $locked->load('items.menuItem.ingredients');

            $availability = $this->checkAvailability($locked);
            if (!$availability['ok']) {
                abort(422, $availability['errors'][0]['message'] ?? 'Insufficient stock for this order.');
            }

            foreach ($locked->items as $orderItem) {
                if ($orderItem->status === 'cancelled') {
                    continue;
                }

                $menuItemName = $orderItem->item_name_snapshot ?? $orderItem->menuItem->name;

                foreach ($orderItem->menuItem->ingredients as $ingredient) {
                    StockMovement::record([
                        'product_id' => $ingredient->product_id,
                        'location_id' => $locked->location_id,
                        'type' => 'recipe_use',
                        'quantity' => $ingredient->quantity * $orderItem->quantity,
                        'reference_type' => 'order',
                        'reference_id' => $locked->id,
                            'notes' => "Order {$locked->order_number} stock deduction ({$menuItemName})",
                    ], $actorId);
                }
            }

            $locked->update([
                'stock_deducted_at' => now(),
            ]);

            return true;
        });
    }

    public function reverseForCancelledOrder(Order $order, string $actorId): bool
    {
        return DB::transaction(function () use ($order, $actorId) {
            $locked = Order::query()->lockForUpdate()->findOrFail($order->id);

            if (!$locked->stock_deducted_at || $locked->stock_reversed_at) {
                return false;
            }

            $locked->load('items.menuItem.ingredients');

            foreach ($locked->items as $orderItem) {
                if ($orderItem->status === 'cancelled') {
                    continue;
                }

                $menuItemName = $orderItem->item_name_snapshot ?? $orderItem->menuItem->name;

                foreach ($orderItem->menuItem->ingredients as $ingredient) {
                    StockMovement::record([
                        'product_id' => $ingredient->product_id,
                        'location_id' => $locked->location_id,
                        'type' => 'restock',
                        'quantity' => $ingredient->quantity * $orderItem->quantity,
                        'reference_type' => 'order_reversal',
                        'reference_id' => $locked->id,
                            'notes' => "Order {$locked->order_number} stock reversal ({$menuItemName})",
                    ], $actorId);
                }
            }

            $locked->update([
                'stock_reversed_at' => now(),
            ]);

            return true;
        });
    }
}
