{{-- resources/views/payments/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Payment History')
@section('page-title', 'Payments')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Payment History</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $booking->booking_number }} — {{ $booking->guest_display_name }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('payments.create', $booking) }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Payment
            </a>
            <a href="{{ route('bookings.show', $booking) }}" 
               class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 text-secondary text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Paid</div>
            <div class="text-2xl font-extrabold text-green-600 mt-1">TZS {{ number_format($totalPaid, 0) }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pending</div>
            <div class="text-2xl font-extrabold text-yellow-600 mt-1">TZS {{ number_format($totalPending, 0) }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Booking Total</div>
            <div class="text-2xl font-extrabold text-primary mt-1">TZS {{ number_format($booking->total_amount, 0) }}</div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        @if($payments->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-secondary">No payments yet</h3>
                <p class="text-xs text-gray-500 mt-1">Click "New Payment" to start collecting payments.</p>
            </div>
        @else
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Payment #</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <a href="{{ route('payments.show', $payment) }}" class="text-sm font-semibold text-primary hover:underline">
                                {{ $payment->payment_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-secondary capitalize">{{ str_replace('-', ' ', $payment->payment_method ?? 'N/A') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-semibold text-secondary">TZS {{ number_format($payment->amount, 0) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $payment->status_badge_class }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-500">{{ $payment->created_at->format('M d, Y g:i A') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('payments.show', $payment) }}" 
                                   class="text-xs text-primary hover:underline font-medium">View</a>
                                @if($payment->isPending())
                                    <a href="{{ route('payments.status', $payment) }}" 
                                       class="text-xs text-yellow-600 hover:underline font-medium">Check</a>
                                @endif
                                @if($payment->canBeRefunded())
                                    <form method="POST" action="{{ route('payments.refund', $payment) }}" class="inline"
                                          onsubmit="return confirm('Are you sure you want to refund this payment?')">
                                        @csrf
                                        <button type="submit" class="text-xs text-red-600 hover:underline font-medium">
                                            Refund
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
