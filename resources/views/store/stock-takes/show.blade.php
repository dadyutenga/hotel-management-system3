@extends('store.layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('store.stock-takes.index') }}" class="text-sm text-primary hover:text-blue-700 font-medium">&larr; Back to Stock-Takes</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Stock-Take Report</h2>
                <p class="text-sm text-gray-500 mt-1 font-mono">{{ $stockTake->stock_take_code }}</p>
            </div>
            <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                {{ $stockTake->status === 'completed' ? 'bg-green-100 text-green-700' : ($stockTake->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                {{ ucfirst($stockTake->status) }}
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500">Initiated By</div>
                <div class="font-medium text-sm mt-1">{{ $stockTake->initiator->name }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500">Started</div>
                <div class="font-medium text-sm mt-1">{{ $stockTake->started_at?->format('M d, H:i') ?? '—' }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500">Completed</div>
                <div class="font-medium text-sm mt-1">{{ $stockTake->completed_at?->format('M d, H:i') ?? '—' }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500">Total Items</div>
                <div class="font-medium text-sm mt-1">{{ $summary['total_items'] }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500">Missing</div>
                <div class="font-medium text-sm mt-1 text-red-600">{{ $summary['missing'] }}</div>
            </div>
        </div>

        <div class="grid grid-cols-3 gap-4 mt-4">
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-green-600">{{ $summary['matched'] }}</div>
                <div class="text-xs text-green-600 font-medium">Matched</div>
            </div>
            <div class="bg-orange-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-orange-600">{{ $summary['variance_positive'] }}</div>
                <div class="text-xs text-orange-600 font-medium">Surplus</div>
            </div>
            <div class="bg-red-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-red-600">{{ $summary['variance_negative'] }}</div>
                <div class="text-xs text-red-600 font-medium">Shortage</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Variance Details</h3>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Beverage</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Barcode</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Expected</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Physical</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Variance</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($stockTake->items as $item)
                <tr class="{{ $item->variance !== 0 ? 'bg-red-50/30' : '' }}">
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $item->beverage->name }}</td>
                    <td class="px-4 py-3"><code class="text-xs bg-gray-100 px-2 py-0.5 rounded font-mono">{{ $item->barcode_scanned }}</code></td>
                    <td class="px-4 py-3 text-center text-sm">{{ $item->expected_quantity }}</td>
                    <td class="px-4 py-3 text-center text-sm font-medium">{{ $item->physical_count }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="font-semibold text-sm {{ $item->variance === 0 ? 'text-green-600' : ($item->variance > 0 ? 'text-orange-600' : 'text-red-600') }}">
                            {{ $item->variance > 0 ? '+' . $item->variance : $item->variance }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                            {{ $item->variance === 0 ? 'bg-green-100 text-green-700' : ($item->variance > 0 ? 'bg-orange-100 text-orange-700' : 'bg-red-100 text-red-700') }}">
                            {{ $item->variance === 0 ? 'Match' : ($item->variance > 0 ? 'Surplus' : 'Shortage') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
