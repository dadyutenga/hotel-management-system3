@extends('store.layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('store.receivings.index') }}" class="text-sm text-primary hover:text-blue-700 font-medium">&larr; Back to Receivings</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Receiving Summary</h2>
                <p class="text-sm text-gray-500 mt-1 font-mono">{{ $receiving->receiving_code }}</p>
            </div>
            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Completed</span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500">Received By</div>
                <div class="font-medium text-sm mt-1">{{ $receiving->receiver->name }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500">Supplier</div>
                <div class="font-medium text-sm mt-1">{{ $receiving->supplier?->name ?? '—' }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500">Date</div>
                <div class="font-medium text-sm mt-1">{{ $receiving->received_at->format('M d, Y H:i') }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500">Total Items</div>
                <div class="font-medium text-sm mt-1">{{ $receiving->items->sum('quantity') }}</div>
            </div>
        </div>

        @if($receiving->notes)
        <div class="mt-4 p-3 bg-blue-50 rounded-lg text-sm text-blue-700">{{ $receiving->notes }}</div>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Items Received</h3>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Beverage</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Barcode</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Category</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Qty</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Unit Price</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($receiving->items as $item)
                <tr>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $item->beverage->name }}</td>
                    <td class="px-4 py-3"><code class="text-xs bg-gray-100 px-2 py-0.5 rounded font-mono">{{ $item->barcode_scanned }}</code></td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $item->beverage->category?->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-center text-sm font-medium">{{ $item->quantity }}</td>
                    <td class="px-4 py-3 text-right text-sm">{{ number_format($item->unit_buying_price, 2) }}</td>
                    <td class="px-4 py-3 text-right text-sm font-medium">{{ number_format($item->quantity * $item->unit_buying_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 border-t border-gray-200">
                <tr>
                    <td colspan="5" class="px-4 py-3 text-right font-semibold text-sm">Grand Total:</td>
                    <td class="px-4 py-3 text-right font-bold text-sm">{{ number_format($receiving->items->sum(fn($i) => $i->quantity * $i->unit_buying_price), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
