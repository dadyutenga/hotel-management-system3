{{-- resources/views/restaurant/reports/popular-items.blade.php --}}
@extends('restaurant.layout')

@section('title', 'Popular Items')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Popular Items</h1>

    {{-- Date range --}}
    <form method="GET" action="{{ route('restaurant.reports.popularItems') }}" class="flex gap-3 items-end mb-6">
        <div>
            <label class="block text-xs text-gray-500 mb-1">From</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}"
                   class="border-gray-300 rounded px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">To</label>
            <input type="date" name="date_to" value="{{ $dateTo }}"
                   class="border-gray-300 rounded px-3 py-2 text-sm">
        </div>
        <button class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700">Filter</button>
    </form>

    {{-- Top items --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium text-gray-600 w-10">#</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Item</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Category</th>
                    <th class="px-4 py-3 font-medium text-gray-600 text-right">Qty Sold</th>
                    <th class="px-4 py-3 font-medium text-gray-600 text-right">Orders</th>
                    <th class="px-4 py-3 font-medium text-gray-600 text-right">Revenue</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($items as $idx => $item)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400 font-bold">{{ $idx + 1 }}</td>
                    <td class="px-4 py-3 font-medium">{{ $item->menuItem->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $item->menuItem->category->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-right font-semibold">{{ number_format($item->total_qty) }}</td>
                    <td class="px-4 py-3 text-right">{{ number_format($item->order_count) }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-primary">@currency($item->total_revenue, 'TZS')</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-400">No data for this period.</td>
                </tr>
                @endforelse
            </tbody>
            @if($items->count())
            <tfoot class="bg-gray-50 font-semibold">
                <tr>
                    <td class="px-4 py-3" colspan="3">Totals</td>
                    <td class="px-4 py-3 text-right">{{ number_format($items->sum('total_qty')) }}</td>
                    <td class="px-4 py-3 text-right">{{ number_format($items->sum('order_count')) }}</td>
                    <td class="px-4 py-3 text-right text-primary">@currency($items->sum('total_revenue'), 'TZS')</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection
