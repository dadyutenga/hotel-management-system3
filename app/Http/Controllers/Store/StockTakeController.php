<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StockTake;
use App\Models\StockTakeItem;
use App\Services\BarcodeStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockTakeController extends Controller
{
    public function __construct(
        protected BarcodeStockService $barcodeService,
    ) {
    }

    public function index()
    {
        $stockTakes = StockTake::with('initiator')
            ->withCount('items')
            ->latest('started_at')
            ->paginate(15);

        return view('store.stock-takes.index', compact('stockTakes'));
    }

    public function create()
    {
        $stockTake = $this->barcodeService->createStockTake(auth()->id());

        return view('store.stock-takes.create', compact('stockTake'));
    }

    public function scan(Request $request, StockTake $stockTake): JsonResponse
    {
        $request->validate([
            'barcode' => 'required|string|min:4|max:100',
        ]);

        try {
            $item = $this->barcodeService->addScanToStockTake($stockTake, $request->barcode);
            $item->load('beverage.category');

            $totalScanned = $stockTake->items()->where('physical_count', '>', 0)->count();

            return response()->json([
                'success' => true,
                'item' => [
                    'id' => $item->id,
                    'beverage_name' => $item->beverage->name,
                    'barcode' => $item->barcode_scanned,
                    'expected_quantity' => $item->expected_quantity,
                    'physical_count' => $item->physical_count,
                    'variance' => $item->variance,
                    'category' => $item->beverage->category?->name,
                ],
                'total_scanned' => $totalScanned,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'success' => false,
                'status' => 'not_found',
                'barcode' => $request->barcode,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function updateCount(Request $request, StockTakeItem $item): JsonResponse
    {
        $request->validate(['physical_count' => 'required|integer|min:0']);

        $item->update([
            'physical_count' => $request->physical_count,
            'variance' => $request->physical_count - $item->expected_quantity,
        ]);

        return response()->json([
            'success' => true,
            'physical_count' => $item->physical_count,
            'variance' => $item->variance,
        ]);
    }

    public function complete(StockTake $stockTake)
    {
        if ($stockTake->status === 'completed') {
            return redirect()->route('store.stock-takes.show', $stockTake)
                ->with('error', 'This stock-take has already been completed.');
        }

        if ($stockTake->status === 'cancelled') {
            return redirect()->route('store.stock-takes.show', $stockTake)
                ->with('error', 'Cannot complete a cancelled stock-take.');
        }

        $this->barcodeService->completeStockTake($stockTake, auth()->id());

        return redirect()->route('store.stock-takes.show', $stockTake)
            ->with('success', 'Stock-take completed. Inventory adjusted for variances.');
    }

    public function cancel(StockTake $stockTake)
    {
        $stockTake->update(['status' => 'cancelled']);

        return redirect()->route('store.stock-takes.index')
            ->with('success', 'Stock-take cancelled.');
    }

    public function show(StockTake $stockTake)
    {
        $stockTake->load('initiator', 'items.beverage.category');

        $summary = [
            'total_items' => $stockTake->items->count(),
            'matched' => $stockTake->items->where('variance', 0)->count(),
            'variance_positive' => $stockTake->items->where('variance', '>', 0)->count(),
            'variance_negative' => $stockTake->items->where('variance', '<', 0)->count(),
            'missing' => $stockTake->items->where('physical_count', 0)->count(),
        ];

        return view('store.stock-takes.show', compact('stockTake', 'summary'));
    }
}
