{{-- resources/views/payments/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Payment Receipt')
@section('page-title', 'Payments')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Payment Receipt</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $payment->payment_number }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('payments.index', $payment->booking) }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 text-secondary text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($payment->isSuccessful())
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                @elseif($payment->isPending())
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-yellow-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                @elseif($payment->isFailed())
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                @elseif($payment->isRefunded())
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                        </svg>
                    </div>
                @else
                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                @endif
                <div>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $payment->status_badge_class }}">
                        {{ ucfirst($payment->status) }}
                    </span>
                </div>
            </div>
            <div class="text-right">
                <div class="text-2xl font-extrabold text-primary">@currency($payment->amount, 'TZS')</div>
                <div class="text-xs text-gray-500">{{ $payment->currency }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Payment Information -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Payment Information</h3>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Payment #</span>
                    <span class="font-medium text-secondary">{{ $payment->payment_number }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Provider</span>
                    <span class="font-medium text-secondary">{{ $payment->provider_label }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Method</span>
                    <span class="font-medium text-secondary capitalize">{{ str_replace('-', ' ', $payment->payment_method ?? 'N/A') }}</span>
                </div>
                @if($payment->provider_reference)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Reference</span>
                    <span class="font-mono text-xs text-secondary">{{ $payment->provider_reference }}</span>
                </div>
                @endif
                @if($payment->charge_type)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Charge Type</span>
                    <span class="font-medium text-secondary">{{ $payment->charge_type_label }}</span>
                </div>
                @endif
            </div>
        </div>

        <!-- Booking Information -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Booking Information</h3>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Booking #</span>
                    <a href="{{ route('bookings.show', $payment->booking) }}" class="font-medium text-primary hover:underline">
                        {{ $payment->booking->booking_number }}
                    </a>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Guest</span>
                    <span class="font-medium text-secondary">{{ $payment->booking->guest_display_name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Room</span>
                    <span class="font-medium text-secondary">{{ $payment->booking->room->room_number ?? 'N/A' }}</span>
                </div>
                @if($payment->bookingCharge)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Charge</span>
                    <span class="font-medium text-secondary">{{ $payment->bookingCharge->description }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Dates -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Timeline</h3>
        <div class="space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Created</span>
                <span class="font-medium text-secondary">{{ $payment->created_at->format('M d, Y g:i A') }}</span>
            </div>
            @if($payment->payment_date)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Paid</span>
                <span class="font-medium text-green-600">{{ $payment->payment_date->format('M d, Y g:i A') }}</span>
            </div>
            @endif
            @if($payment->refunded_at)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Refunded</span>
                <span class="font-medium text-purple-600">{{ $payment->refunded_at->format('M d, Y g:i A') }}</span>
            </div>
            @endif
            @if($payment->creator)
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Initiated By</span>
                <span class="font-medium text-secondary">{{ $payment->creator->name }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    @if($payment->canBeRefunded() || $payment->isPending())
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Actions</h3>
        <div class="flex items-center gap-3">
            @if($payment->isPending())
                <a href="{{ route('payments.status', $payment) }}"
                   class="px-4 py-2.5 bg-yellow-600 text-white text-sm font-semibold rounded-xl hover:bg-yellow-700 transition-all">
                    Check Status
                </a>
            @endif
            @if($payment->canBeRefunded())
                <form method="POST" action="{{ route('payments.refund', $payment) }}" class="inline"
                      onsubmit="return confirm('Are you sure you want to refund @currency($payment->amount, 'TZS')?')">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition-all">
                        Full Refund
                    </button>
                </form>
            @endif
        </div>
    </div>
    @endif
</div>
@endsection
