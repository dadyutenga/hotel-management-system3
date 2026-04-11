@extends('layouts.app')

@section('title', 'Incoming Drink Orders')
@section('page-title', 'Incoming Drink Orders')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="p-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap gap-2">
            <select name="source" class="border-gray-300 rounded-lg text-sm px-3 py-2">
                <option value="">All Sources</option>
                @foreach(['walkin' => 'Walk-in', 'restaurant' => 'Restaurant', 'room_service' => 'Room Service'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('source') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="border-gray-300 rounded-lg text-sm px-3 py-2">
                <option value="">All Statuses</option>
                @foreach(['pending', 'accepted', 'prepared', 'served', 'cancelled', 'rejected'] as $status)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button class="px-3 py-2 text-sm rounded-lg bg-gray-100 hover:bg-gray-200">Filter</button>
        </form>

        <div class="flex gap-2 text-sm">
            <a href="{{ route('bartender.orders.walkin.create') }}" class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">New Walk-in</a>
            <a href="{{ route('bartender.orders.room-service.create') }}" class="px-3 py-2 rounded-lg border border-gray-300 hover:bg-gray-50">New Room Service</a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">Source</th>
                    <th class="px-4 py-3 text-left">Reference</th>
                    <th class="px-4 py-3 text-left">Items</th>
                    <th class="px-4 py-3 text-left">Requested Time</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-4 py-3">{{ ucwords(str_replace('_', ' ', $order->order_source ?? 'unknown')) }}</td>
                        <td class="px-4 py-3">
                            @if($order->order_source === 'restaurant')
                                {{ $order->order_number }}
                            @elseif($order->order_source === 'room_service')
                                {{ $order->booking?->booking_number ?? $order->order_number }}
                            @else
                                {{ $order->order_number }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-600">
                            {{ $order->items->map(fn($i) => $i->menuItem->name . ' x' . $i->quantity)->implode(', ') }}
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">{{ ucfirst($order->bartender_status ?? 'pending') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('bartender.orders.show', $order) }}" class="text-blue-600 hover:underline">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No incoming drink orders.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $orders->links() }}</div>
</div>
@endsection
