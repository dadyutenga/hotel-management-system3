{{-- resources/views/restaurant/reports/daily-sales.blade.php --}}
@extends('restaurant.layout')

@section('title', 'Daily Sales Report')

@section('content')
<div class="max-w-5xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Daily Sales Report</h1>

    {{-- Date picker --}}
    <form method="GET" action="{{ route('restaurant.reports.dailySales') }}" class="flex gap-3 items-end mb-6">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Date</label>
            <input type="date" name="date" value="{{ $date }}"
                   class="border-gray-300 rounded px-3 py-2 text-sm">
        </div>
        <button class="bg-gray-800 text-white px-4 py-2 rounded text-sm hover:bg-gray-700">View</button>
    </form>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-xs text-gray-500 mb-1">Total Orders</p>
            <p class="text-2xl font-bold text-gray-800">{{ $summary['total_orders'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-xs text-gray-500 mb-1">Total Revenue</p>
            <p class="text-2xl font-bold text-primary">{{ number_format($summary['total_revenue'], 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-xs text-gray-500 mb-1">Cash</p>
            <p class="text-xl font-bold text-green-600">{{ number_format($summary['cash_revenue'], 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-xs text-gray-500 mb-1">Card</p>
            <p class="text-xl font-bold text-blue-600">{{ number_format($summary['card_revenue'], 0) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <p class="text-xs text-gray-500 mb-1">Guest Charges</p>
            <p class="text-xl font-bold text-purple-600">{{ number_format($summary['guest_charges'], 0) }}</p>
        </div>
    </div>

    {{-- Orders table --}}
    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium text-gray-600">Order #</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Section</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Table</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Type</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Items</th>
                    <th class="px-4 py-3 font-medium text-gray-600 text-right">Total</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Payment</th>
                    <th class="px-4 py-3 font-medium text-gray-600">Settled At</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($orders as $order)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-mono text-xs">
                        <a href="{{ route('restaurant.orders.show', $order) }}" class="text-primary hover:underline">
                            {{ $order->order_number }}
                        </a>
                    </td>
                    <td class="px-4 py-3">{{ $order->location->name ?? '—' }}</td>
                    <td class="px-4 py-3">{{ $order->table->table_number ?? '—' }}</td>
                    <td class="px-4 py-3">{{ ucfirst($order->order_type) }}</td>
                    <td class="px-4 py-3 text-center">{{ $order->items->count() }}</td>
                    <td class="px-4 py-3 text-right font-semibold">{{ number_format($order->total, 0) }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full
                            {{ $order->payment_method === 'cash' ? 'bg-green-100 text-green-700' : '' }}
                            {{ $order->payment_method === 'card' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $order->payment_method === 'charge_to_booking' ? 'bg-purple-100 text-purple-700' : '' }}">
                            {{ str_replace('_', ' ', ucfirst($order->payment_method ?? '—')) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500">
                        {{ $order->settled_at ? \Carbon\Carbon::parse($order->settled_at)->format('H:i') : '—' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-400">No settled orders for this date.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
