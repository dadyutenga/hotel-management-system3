@extends('layouts.app')

@section('title', 'Drink Orders Inbox')
@section('page-title', 'Drink Orders Inbox')

@php
$statusLabels = [
    'pending'   => 'Pending',
    'accepted'  => 'Accepted',
    'prepared'  => 'Prepared',
    'served'    => 'Served',
    'cancelled' => 'Cancelled',
    'rejected'  => 'Rejected',
];
$statusColors = [
    'pending'   => 'bg-yellow-100 text-yellow-700',
    'accepted'  => 'bg-blue-100 text-blue-700',
    'prepared'  => 'bg-purple-100 text-purple-700',
    'served'    => 'bg-green-100 text-green-700',
    'cancelled' => 'bg-red-100 text-red-600',
    'rejected'  => 'bg-red-100 text-red-600',
];
@endphp

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="p-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap gap-2">
            <select name="status" class="border-gray-300 rounded-lg text-sm px-3 py-2">
                <option value="">All Statuses</option>
                @foreach($statusLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="px-3 py-2 text-sm rounded-lg bg-gray-100 hover:bg-gray-200">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">Order #</th>
                    <th class="px-4 py-3 text-left">Room</th>
                    <th class="px-4 py-3 text-left">Guest</th>
                    <th class="px-4 py-3 text-left">Items</th>
                    <th class="px-4 py-3 text-left">Requested</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-center">Guest Done</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $order->order_number }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $order->booking->room->room_number ?? '—' }}</td>
                        <td class="px-4 py-3 text-gray-700">{{ $order->booking->guest_name ?? $order->customer_name }}</td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $order->items->map(fn($i) => ($i->menuItem?->name ?? $i->item_name_snapshot) . ' x' . $i->quantity)->implode(', ') }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs">{{ $order->created_at->format('d M H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs {{ $statusColors[$order->bartender_status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ $statusLabels[$order->bartender_status] ?? $order->bartender_status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($order->guest_completed_at)
                                <span class="text-xs text-green-600 font-medium">Yes</span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('bartender.orders.show', $order) }}" class="text-blue-600 hover:underline text-xs">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-4 py-8 text-center text-gray-400">No drink orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $orders->withQueryString()->links() }}</div>
</div>
@endsection
