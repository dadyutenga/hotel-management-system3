@extends('store.layout')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('store.beverages.index') }}" class="text-sm text-primary hover:text-blue-700 font-medium">&larr; Back to Beverages</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $beverage->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $beverage->category?->name ?? 'Uncategorized' }} &middot; {{ ucfirst($beverage->unit) }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('store.beverages.edit', $beverage) }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">Edit</a>
                <form method="POST" action="{{ route('store.beverages.destroy', $beverage) }}" onsubmit="return confirm('Delete this beverage?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-sm font-medium">Delete</button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500 font-medium">Barcode</div>
                <div class="font-mono text-sm mt-1">{{ $beverage->barcode }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500 font-medium">Buying Price</div>
                <div class="font-semibold text-lg mt-1">{{ number_format($beverage->buying_price, 2) }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500 font-medium">Selling Price</div>
                <div class="font-semibold text-lg mt-1">{{ number_format($beverage->selling_price, 2) }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="text-xs text-gray-500 font-medium">Stock on Hand</div>
                @php $qty = $beverage->inventory?->quantity_on_hand ?? 0; @endphp
                <div class="font-semibold text-lg mt-1 {{ $qty <= $beverage->reorder_level ? 'text-red-600' : 'text-green-600' }}">{{ $qty }}</div>
                <div class="text-xs text-gray-400">Reorder at {{ $beverage->reorder_level }}</div>
            </div>
        </div>

        @if($beverage->description)
        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
            <div class="text-xs text-gray-500 font-medium mb-1">Description</div>
            <p class="text-sm text-gray-700">{{ $beverage->description }}</p>
        </div>
        @endif
    </div>

    @if($beverage->stockMovements->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Recent Movements</h3>
        </div>
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Type</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Qty</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Reference</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">By</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($beverage->stockMovements->take(10) as $movement)
                <tr>
                    <td class="px-4 py-2">
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                            {{ $movement->movement_type === 'IN' ? 'bg-green-100 text-green-700' : ($movement->movement_type === 'OUT' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                            {{ $movement->movement_type }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-sm">{{ $movement->quantity }}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">{{ $movement->reference_type }} #{{ Str::limit($movement->reference_id, 8) }}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">{{ $movement->performer?->name ?? '—' }}</td>
                    <td class="px-4 py-2 text-sm text-gray-500">{{ $movement->created_at->format('M d, Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
