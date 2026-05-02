@extends('layouts.app')

@section('title', __('bartender.titles.inbox'))
@section('page-title', __('bartender.titles.inbox'))

@php($sourceLabels = ['walkin' => __('bartender.sources.walkin'), 'restaurant' => __('bartender.sources.restaurant'), 'room_service' => __('bartender.sources.room_service')])
@php($statusLabels = ['pending' => __('bartender.statuses.pending'), 'accepted' => __('bartender.statuses.accepted'), 'prepared' => __('bartender.statuses.prepared'), 'served' => __('bartender.statuses.served'), 'cancelled' => __('bartender.statuses.cancelled'), 'rejected' => __('bartender.statuses.rejected')])

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="p-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex flex-wrap gap-2">
            <select name="source" class="border-gray-300 rounded-lg text-sm px-3 py-2">
                <option value="">{{ __('bartender.sources.all') }}</option>
                @foreach($sourceLabels as $value => $label)
                    <option value="{{ $value }}" @selected(request('source') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="status" class="border-gray-300 rounded-lg text-sm px-3 py-2">
                <option value="">{{ __('bartender.statuses.all') }}</option>
                @foreach($statusLabels as $status => $label)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="px-3 py-2 text-sm rounded-lg bg-gray-100 hover:bg-gray-200">{{ __('bartender.actions.filter') }}</button>
        </form>

        <div class="flex gap-2 text-sm">
            <a href="{{ route('bartender.pos') }}" class="px-3 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">{{ __('bartender.actions.new_walkin') }}</a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.source') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.reference') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.items') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.requested_time') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.status') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('bartender.fields.action') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($orders as $order)
                    <tr>
                        <td class="px-4 py-3">{{ $sourceLabels[$order->order_source ?? 'unknown'] ?? __('bartender.sources.unknown') }}</td>
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
                            <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">{{ $statusLabels[$order->bartender_status ?? 'pending'] ?? __('bartender.statuses.pending') }}</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('bartender.orders.show', $order) }}" class="text-blue-600 hover:underline">{{ __('bartender.actions.open') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">{{ __('bartender.messages.no_orders') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $orders->links() }}</div>
</div>
@endsection
