@extends('finance.layout')
@section('title', 'Financial Dashboard')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Financial Dashboard</h1>
    <form method="GET" class="flex gap-2">
        <input type="date" name="date_from" value="{{ $dateFrom }}" class="border rounded px-3 py-1.5 text-sm">
        <input type="date" name="date_to"   value="{{ $dateTo }}"   class="border rounded px-3 py-1.5 text-sm">
        <button class="bg-blue-600 text-white px-4 py-1.5 rounded text-sm hover:bg-blue-700">Apply</button>
    </form>
</div>

{{-- Today's summary cards --}}
<div class="grid grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Today Revenue</p>
        <p class="text-2xl font-bold text-gray-800 mt-1">
            ${{ number_format($todaySummary['total_revenue'], 2) }}
        </p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Checkout</p>
        <p class="text-2xl font-bold text-green-600 mt-1">
            ${{ number_format($todaySummary['checkout_revenue'], 2) }}
        </p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Walk-in Sales</p>
        <p class="text-2xl font-bold text-blue-600 mt-1">
            ${{ number_format($todaySummary['walkin_revenue'], 2) }}
        </p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Cash</p>
        <p class="text-2xl font-bold text-gray-700 mt-1">
            ${{ number_format($todaySummary['cash_total'], 2) }}
        </p>
    </div>
    <div class="bg-white rounded shadow p-4">
        <p class="text-xs text-gray-400 uppercase tracking-wide">Card</p>
        <p class="text-2xl font-bold text-gray-700 mt-1">
            ${{ number_format($todaySummary['card_total'], 2) }}
        </p>
    </div>
</div>

<div class="grid grid-cols-3 gap-6 mb-6">
    {{-- Revenue by module --}}
    <div class="bg-white rounded shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">Revenue by Module</h2>
        @forelse($revenueByModule as $row)
        <div class="flex justify-between items-center py-2 border-b last:border-0 text-sm">
            <span class="text-gray-600 capitalize">{{ str_replace('_', ' ', $row->source_module) }}</span>
            <span class="font-medium">${{ number_format($row->total_usd, 2) }}</span>
        </div>
        @empty
        <p class="text-sm text-gray-400">No data for selected period.</p>
        @endforelse
    </div>

    {{-- Revenue by payment method --}}
    <div class="bg-white rounded shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">By Payment Method</h2>
        @forelse($revenueByMethod as $row)
        <div class="flex justify-between items-center py-2 border-b last:border-0 text-sm">
            <span class="text-gray-600 capitalize">{{ ucfirst($row->payment_method) }}</span>
            <span class="font-medium">${{ number_format($row->total_usd, 2) }}</span>
        </div>
        @empty
        <p class="text-sm text-gray-400">No data for selected period.</p>
        @endforelse
    </div>

    {{-- Outstanding --}}
    <div class="bg-white rounded shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">Outstanding Balance</h2>
        <p class="text-3xl font-bold text-red-500">${{ number_format($outstandingTotal, 2) }}</p>
        <p class="text-sm text-gray-400 mt-2">Unpaid guest charges pending checkout</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">Completed Orders Missing Charges</h2>
        <div class="space-y-2 text-sm">
            @php($missingChargeCount = $ordersMissingCharges->count() + $laundryMissingCharges->count())

            @forelse($ordersMissingCharges as $order)
                <div class="flex justify-between items-start border-b last:border-0 pb-2">
                    <div>
                        <p class="font-medium text-gray-800">{{ $order->order_number }}</p>
                        <p class="text-gray-500">{{ $order->booking?->booking_number }} · {{ $order->location?->name }} · {{ ucfirst($order->status) }}</p>
                    </div>
                    <span class="text-red-600 font-medium">Missing charge</span>
                </div>
            @empty @endforelse

            @forelse($laundryMissingCharges as $order)
                <div class="flex justify-between items-start border-b last:border-0 pb-2">
                    <div>
                        <p class="font-medium text-gray-800">{{ $order->order_number }}</p>
                        <p class="text-gray-500">{{ $order->booking?->booking_number }} · Laundry · {{ ucfirst($order->status) }}</p>
                    </div>
                    <span class="text-red-600 font-medium">Missing charge</span>
                </div>
            @empty @endforelse

            @if($missingChargeCount === 0)
                <p class="text-sm text-gray-400">No completed module orders are missing billing charges.</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">Unpaid Charges by Booking</h2>
        <div class="space-y-2 text-sm">
            @forelse($unpaidChargesByBooking as $row)
                <div class="flex justify-between items-start border-b last:border-0 pb-2">
                    <div>
                        <p class="font-medium text-gray-800">{{ $row->booking?->booking_number ?? 'Unknown Booking' }}</p>
                        <p class="text-gray-500">{{ $row->charge_count }} unpaid charge(s)</p>
                    </div>
                    <span class="font-medium text-amber-700">TZS {{ number_format($row->total_tzs, 0) }}</span>
                </div>
            @empty
                <p class="text-sm text-gray-400">No unpaid booking charges.</p>
            @endforelse
        </div>
    </div>
</div>

{{-- Recent transactions --}}
<div class="bg-white rounded shadow overflow-hidden">
    <div class="px-5 py-4 border-b">
        <h2 class="font-semibold text-gray-700">Recent Transactions</h2>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-3 text-left text-gray-500">TXN #</th>
                <th class="px-4 py-3 text-left text-gray-500">Type</th>
                <th class="px-4 py-3 text-left text-gray-500">Module</th>
                <th class="px-4 py-3 text-left text-gray-500">Method</th>
                <th class="px-4 py-3 text-right text-gray-500">USD</th>
                <th class="px-4 py-3 text-left text-gray-500">By</th>
                <th class="px-4 py-3 text-left text-gray-500">Time</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($recentTransactions as $txn)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-xs">{{ $txn->transaction_number }}</td>
                <td class="px-4 py-3">
                    <span class="px-2 py-0.5 rounded text-xs
                        {{ $txn->type === 'checkout_payment' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                        {{ ucwords(str_replace('_', ' ', $txn->type)) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-gray-500 capitalize">{{ $txn->source_module }}</td>
                <td class="px-4 py-3 text-gray-500 capitalize">{{ $txn->payment_method }}</td>
                <td class="px-4 py-3 text-right font-medium">${{ number_format($txn->amount_usd, 2) }}</td>
                <td class="px-4 py-3 text-gray-500 text-xs">{{ $txn->actor?->name }}</td>
                <td class="px-4 py-3 text-gray-400 text-xs">{{ $txn->created_at->format('H:i d M') }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No transactions yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
