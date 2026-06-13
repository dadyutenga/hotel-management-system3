@extends('pos.layout')

@section('page-title', 'Cashier - Open Orders')

@section('header-actions')
<a href="{{ route('pos.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-colors">
    Dashboard
</a>
@endsection

@section('content')
<div class="space-y-4">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <h3 class="text-lg font-bold text-gray-800">Orders from Waiters</h3>
        <p class="text-sm text-gray-500">Review, settle, or charge orders placed by waiters via passkey login.</p>
    </div>

    @if($orders->count())
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($orders as $order)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4" x-data="{ showSettle: false }">
            <div class="flex items-start justify-between mb-3">
                <div>
                    <div class="text-lg font-bold text-primary font-mono">{{ $order->order_number }}</div>
                    <div class="text-xs text-gray-500">{{ $order->created_at->format('M d, H:i') }}</div>
                </div>
                <span class="px-2 py-1 rounded-lg text-xs font-semibold
                    {{ $order->status === 'served' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            <div class="text-sm text-gray-600 mb-2">
                <span class="font-medium">Waiter:</span> {{ $order->creator?->name ?? 'Unknown' }}
            </div>
            <div class="text-sm text-gray-600 mb-2">
                <span class="font-medium">Customer:</span> {{ $order->customer_name }}
            </div>
            @if($order->booking)
            <div class="text-sm text-gray-600 mb-2">
                <span class="font-medium">Booking:</span> {{ $order->booking->booking_number }}
            </div>
            @endif

            <div class="border-t border-gray-100 my-3 pt-3">
                <table class="w-full text-sm">
                    <tbody>
                        @foreach($order->items as $item)
                        <tr class="border-b border-gray-50 last:border-0">
                            <td class="py-1">{{ $item->item_name_snapshot }}</td>
                            <td class="py-1 text-right text-gray-500">x{{ $item->quantity }}</td>
                            <td class="py-1 text-right font-medium">@currency($item->subtotal, 'TZS')</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-gray-500">Total</span>
                <span class="text-lg font-bold text-gray-800">@currency($order->total, 'TZS')</span>
            </div>

            <div class="flex gap-2">
                <button type="button" @click="showSettle = !showSettle"
                    class="flex-1 px-3 py-2 bg-primary hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition-colors">
                    Settle / Charge
                </button>
                <form method="POST" action="{{ route('pos.cashier.cancel', $order) }}" class="inline"
                    onsubmit="return confirm('Cancel this order?')">
                    @csrf
                    <button type="submit" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg text-sm font-semibold transition-colors">
                        Cancel
                    </button>
                </form>
            </div>

            <form x-show="showSettle" x-cloak method="POST" action="{{ route('pos.cashier.settle', $order) }}" class="mt-3 space-y-3 border-t border-gray-100 pt-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Method</label>
                    <select name="payment_method" x-model="paymentMethod{{ $loop->index }}"
                        class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
                        <option value="cash">Cash</option>
                        <option value="mobile">Mobile Money</option>
                        <option value="card">Card</option>
                        <option value="charge_to_booking">Charge to Room</option>
                    </select>
                </div>

                <div x-show="paymentMethod{{ $loop->index }} === 'charge_to_booking'" x-cloak>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Checked-in Guest</label>
                    <select name="booking_id" class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
                        <option value="">Select guest</option>
                        @foreach($activeBookings as $booking)
                        <option value="{{ $booking->id }}" {{ $order->booking_id === $booking->id ? 'selected' : '' }}>
                            {{ $booking->guest_name }} — {{ $booking->booking_number }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition-colors">
                    Confirm
                </button>
            </form>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <h3 class="text-lg font-semibold text-gray-700">No open orders</h3>
        <p class="text-sm text-gray-500 mt-1">Waiters have not placed any orders yet.</p>
    </div>
    @endif
</div>
@endsection
