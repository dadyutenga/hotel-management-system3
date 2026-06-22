<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\StockReceiving;
use App\Models\StockReceivingItem;
use App\Models\Supplier;
use App\Services\BarcodeStockService;
use App\Services\BuildingContext;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockReceivingController extends Controller
{
    public function __construct(
        protected BarcodeStockService $barcodeService,
    ) {}

    public function index()
    {
        $receivings = StockReceiving::with('receiver', 'supplier', 'items.beverage')
            ->forUserBuilding()
            ->latest('received_at')
            ->paginate(15);

        return view('store.receivings.index', compact('receivings'));
    }

    public function create()
    {
        $receiving = $this->barcodeService->createReceiving(auth()->id());
        $receiving->update(['building_id' => BuildingContext::buildingId()]);

        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('store.receivings.create', compact('receiving', 'suppliers'));
    }

    public function scan(Request $request, StockReceiving $receiving): JsonResponse
    {
        $request->validate([
            'barcode' => 'required|string|min:4|max:100',
        ]);

        BuildingContext::enforce($receiving->building_id);

        try {
            $item = $this->barcodeService->addScanToReceiving($receiving, $request->barcode);
            BuildingContext::enforce($item->beverage->building_id);
            $item->load('beverage.category');

            $totalItems = $receiving->items()->sum('quantity');

            return response()->json([
                'success' => true,
                'item' => [
                    'id' => $item->id,
                    'beverage_name' => $item->beverage->name,
                    'barcode' => $item->barcode_scanned,
                    'quantity' => $item->quantity,
                    'unit_buying_price' => $item->unit_buying_price,
                    'category' => $item->beverage->category?->name,
                    'unit' => $item->beverage->unit,
                ],
                'total_items' => $totalItems,
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

    public function updateItem(Request $request, StockReceivingItem $item): JsonResponse
    {
        BuildingContext::enforce($item->receiving->building_id);

        $request->validate(['quantity' => 'required|integer|min:1']);

        $this->barcodeService->updateReceivingItemQuantity($item, $request->quantity);

        return response()->json([
            'success' => true,
            'quantity' => $item->fresh()->quantity,
        ]);
    }

    public function removeItem(StockReceivingItem $item): JsonResponse
    {
        BuildingContext::enforce($item->receiving->building_id);

        $item->delete();

        return response()->json(['success' => true]);
    }

    public function confirm(Request $request, StockReceiving $receiving)
    {
        BuildingContext::enforce($receiving->building_id);

        $request->validate([
            'supplier_id' => 'nullable|uuid|exists:suppliers,id',
            'notes' => 'nullable|string',
        ]);

        if ($receiving->status === 'completed') {
            return redirect()->route('store.receivings.show', $receiving)
                ->with('error', 'This receiving has already been confirmed.');
        }

        if ($receiving->items->isEmpty()) {
            return redirect()->route('store.receivings.create')
                ->with('error', 'No items scanned. Please scan at least one beverage.');
        }

        if ($request->supplier_id) {
            $receiving->update(['supplier_id' => $request->supplier_id]);
        }
        if ($request->notes) {
            $receiving->update(['notes' => $request->notes]);
        }

        $this->barcodeService->confirmReceiving($receiving, auth()->id());

        return redirect()->route('store.receivings.show', $receiving)
            ->with('success', 'Stock receiving confirmed and inventory updated.');
    }

    public function show(StockReceiving $receiving)
    {
        BuildingContext::enforce($receiving->building_id);

        $receiving->load('receiver', 'supplier', 'items.beverage.category');

        return view('store.receivings.show', compact('receiving'));
    }
}
