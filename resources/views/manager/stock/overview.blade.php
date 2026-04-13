@extends('layouts.app')

@section('title', 'Stock Overview')
@section('page-title', 'Stock Overview')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Stock Overview Dashboard</h2>
            <p class="mt-1 text-sm text-gray-500">Monitor current stock levels, low stock alerts, and category health</p>
        </div>
        <form method="GET" class="flex gap-3">
            <select name="location_id" class="rounded-lg border border-gray-300 px-4 py-2.5">
                <option value="">All Locations</option>
                @foreach($locations as $location)
                <option value="{{ $location->id }}" @selected(request('location_id') === $location->id)>{{ $location->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Apply</button>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-lg">
            <div class="text-sm text-gray-500">Tracked Items</div>
            <div class="mt-2 text-3xl font-extrabold text-secondary">{{ $levels->total() }}</div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-lg">
            <div class="text-sm text-gray-500">Low Stock Alerts</div>
            <div class="mt-2 text-3xl font-extrabold text-amber-600">{{ $lowStockCount }}</div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-lg">
            <div class="text-sm text-gray-500">Out of Stock</div>
            <div class="mt-2 text-3xl font-extrabold text-red-600">{{ $outOfStockCount }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gradient-to-r from-indigo-50 to-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Item</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Location</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Quantity</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Reorder</th>
                        <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($levels as $level)
                    <tr class="hover:bg-indigo-50/40 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-secondary">{{ $level->product->name }}</div>
                            <div class="text-xs text-gray-500">{{ $level->product->category ?: 'Uncategorized' }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-secondary">{{ $level->location->name }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-secondary">{{ number_format($level->quantity, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ number_format($level->product->reorder_level, 2) }}</td>
                        <td class="px-6 py-4">
                            @php($isOut = $level->quantity <= 0)
                            @php($isLow = !$isOut && $level->quantity <= $level->product->reorder_level)
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $isOut ? 'bg-red-100 text-red-600' : ($isLow ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700') }}">
                                {{ $isOut ? 'Out of stock' : ($isLow ? 'Low stock' : 'Healthy') }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @if($levels->hasPages())
            <div class="border-t border-gray-100 px-6 py-4">{{ $levels->links() }}</div>
            @endif
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-lg">
            <h3 class="mb-4 text-lg font-bold text-secondary">Category Summary</h3>
            <div class="space-y-4">
                @forelse($categorySummary as $category)
                <div class="rounded-xl border border-gray-100 p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-semibold text-secondary">{{ $category->category }}</span>
                        <span class="text-xs text-gray-500">{{ $category->items_count }} items</span>
                    </div>
                    <div class="mt-2 text-sm text-gray-600">Total quantity: {{ number_format($category->total_quantity, 2) }}</div>
                </div>
                @empty
                <p class="text-sm text-gray-500">No category data found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
