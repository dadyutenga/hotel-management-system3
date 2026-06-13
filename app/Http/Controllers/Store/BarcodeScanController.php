<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Services\BarcodeStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BarcodeScanController extends Controller
{
    public function __construct(
        protected BarcodeStockService $barcodeService,
    ) {
    }

    public function scan(Request $request): JsonResponse
    {
        $request->validate([
            'barcode' => 'required|string|min:4|max:100',
        ]);

        $beverage = $this->barcodeService->resolveBarcode($request->barcode);

        if ($beverage) {
            $beverage->load('category');

            return response()->json([
                'status' => 'found',
                'beverage' => [
                    'id' => $beverage->id,
                    'name' => $beverage->name,
                    'barcode' => $beverage->barcode,
                    'category' => $beverage->category?->name,
                    'unit' => $beverage->unit,
                    'buying_price' => $beverage->buying_price,
                    'selling_price' => $beverage->selling_price,
                    'quantity_on_hand' => $beverage->quantity_on_hand,
                ],
            ]);
        }

        return response()->json([
            'status' => 'not_found',
            'barcode' => $request->barcode,
        ]);
    }
}
