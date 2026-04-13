@extends('layouts.app')

@section('title', 'Stock Movements')
@section('page-title', 'Stock Movements')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Stock Movement Audit</h2>
            <p class="mt-1 text-sm text-gray-500">Review incoming and outgoing stock activity across all monitored locations</p>
        </div>
        <form method="GET" class="grid grid-cols-1 gap-3 md:grid-cols-4">
            <select name="location_id" class="rounded-lg border border-gray-300 px-4 py-2.5">
                <option value="">All Locations</option>
                @foreach($locations as $location)
                <option value="{{ $location->id }}" @selected(request('location_id') === $location->id)>{{ $location->name }}</option>
                @endforeach
            </select>
            <select name="type" class="rounded-lg border border-gray-300 px-4 py-2.5">
                <option value="">All Types</option>
                @foreach(['restock', 'damage', 'adjustment', 'transfer_in', 'transfer_out', 'issue'] as $type)
                <option value="{{ $type }}" @selected(request('type') === $type)>{{ str_replace('_', ' ', ucfirst($type)) }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border border-gray-300 px-4 py-2.5">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border border-gray-300 px-4 py-2.5">
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">Filter</button>
        </form>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-indigo-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Date</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Item</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Location</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Type</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Source</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Quantity</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Actor</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($movements as $movement)
                <tr class="hover:bg-indigo-50/40 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $movement->created_at->format('M d, Y H:i') }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-secondary">{{ $movement->product->name }}</div>
                        <div class="text-xs text-gray-500">{{ $movement->product->sku }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-secondary">{{ $movement->location->name }}</td>
                    <td class="px-6 py-4 text-sm text-secondary">{{ str_replace('_', ' ', ucfirst($movement->type)) }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $movement->reference_type ? str_replace('_', ' ', ucfirst($movement->reference_type)) : 'Manual' }}</td>
                    <td class="px-6 py-4 text-sm font-semibold {{ in_array($movement->type, ['restock', 'transfer_in']) ? 'text-green-600' : 'text-red-600' }}">
                        {{ in_array($movement->type, ['restock', 'transfer_in']) ? '+' : '-' }}{{ number_format($movement->quantity, 2) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-secondary">{{ $movement->actor->name ?? 'System' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-sm text-gray-500">No stock movements found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($movements->hasPages())
        <div class="border-t border-gray-100 px-6 py-4">{{ $movements->links() }}</div>
        @endif
    </div>
</div>
@endsection
