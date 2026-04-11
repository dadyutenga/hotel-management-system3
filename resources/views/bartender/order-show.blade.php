@extends('layouts.app')

@section('title', 'Drink Order ' . $order->order_number)
@section('page-title', 'Drink Order ' . $order->order_number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Order Details</h2>
                    <p class="text-xs text-gray-500">Source: {{ ucwords(str_replace('_', ' ', $order->order_source ?? 'unknown')) }} | Requested: {{ $order->created_at->format('Y-m-d H:i') }}</p>
                </div>
                <span class="px-2 py-1 rounded-full text-xs bg-gray-100 text-gray-700">{{ ucfirst($order->bartender_status ?? 'pending') }}</span>
            </div>

            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-3 py-2 text-left">Item</th>
                        <th class="px-3 py-2 text-right">Qty</th>
                        <th class="px-3 py-2 text-right">Price</th>
                        <th class="px-3 py-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-3 py-2">{{ $item->menuItem->name }}</td>
                            <td class="px-3 py-2 text-right">{{ $item->quantity }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($item->unit_price, 0) }}</td>
                            <td class="px-3 py-2 text-right font-semibold">{{ number_format($item->subtotal, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-right mt-3 text-lg font-bold text-gray-800">Total: {{ number_format($order->total, 0) }} TZS</div>
        </div>

        @if(!$availability['ok'])
            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                <h3 class="text-sm font-semibold text-red-700 mb-2">Stock Availability Check Failed</h3>
                <ul class="text-sm text-red-600 list-disc list-inside">
                    @foreach($availability['errors'] as $error)
                        <li>{{ $error['message'] }}</li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">
                All order lines have enough stock for processing.
            </div>
        @endif
    </div>

    <div class="space-y-4">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <h3 class="font-semibold text-gray-800 mb-3">Actions</h3>

            @if(($order->bartender_status ?? 'pending') === 'pending')
                <form method="POST" action="{{ route('bartender.orders.accept', $order) }}" class="mb-2">@csrf
                    <button class="w-full py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Accept Order</button>
                </form>
                <form method="POST" action="{{ route('bartender.orders.reject', $order) }}" class="mb-2">@csrf
                    <button class="w-full py-2 rounded-lg border border-red-300 text-red-600 hover:bg-red-50">Reject</button>
                </form>
            @endif

            @if($order->bartender_status === 'accepted')
                <form method="POST" action="{{ route('bartender.orders.prepare', $order) }}" class="mb-2">@csrf
                    <button class="w-full py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">Mark Prepared</button>
                </form>
            @endif

            @if($order->bartender_status === 'prepared')
                <form method="POST" action="{{ route('bartender.orders.serve', $order) }}" class="mb-2">@csrf
                    <button class="w-full py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">Mark Served</button>
                </form>
            @endif

            @if(!in_array($order->bartender_status, ['served', 'cancelled', 'rejected']))
                <form method="POST" action="{{ route('bartender.orders.cancel', $order) }}">@csrf
                    <button class="w-full py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancel Order</button>
                </form>
            @endif
        </div>

        @if($order->order_source === 'walkin' && !in_array($order->status, ['settled', 'cancelled']) && $order->bartender_status === 'prepared')
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
                <h3 class="font-semibold text-gray-800 mb-3">Walk-in Payment</h3>
                <x-walkin-payment-modal
                    :amount="$order->total"
                    :order-id="$order->id"
                    :order-number="$order->order_number"
                    module="bar"
                    :customer-name="$order->customer_name ?? ''"
                    :customer-phone="$order->booking?->guest_phone ?? ''"
                />
            </div>
        @elseif($order->order_source === 'walkin' && !in_array($order->status, ['settled', 'cancelled']))
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-700">
                Accept and prepare the drink order before opening walk-in payment.
            </div>
        @elseif($order->order_source === 'walkin' && $order->status === 'settled')
            <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">
                Walk-in payment has been completed. Receipt is available from the order receipt flow.
            </div>
        @elseif($order->order_source === 'restaurant')
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-700">
                This order links to restaurant billing flow and settles with restaurant cashier.
            </div>
        @elseif($order->order_source === 'room_service')
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-700">
                On serve, charge is posted to guest folio for unified checkout.
            </div>
        @endif
    </div>
</div>
@endsection
