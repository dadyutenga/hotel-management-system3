{{-- resources/views/payments/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Make Payment')
@section('page-title', 'Payments')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Make Payment</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $booking->booking_number }} — {{ $booking->guest_display_name }}</p>
        </div>
        <a href="{{ route('bookings.show', $booking) }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 border border-gray-200 text-secondary text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    <!-- Payment Summary -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Payment Summary</h3>
        <div class="space-y-3">
            @if($charge)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Charge Type</span>
                    <span class="font-medium text-secondary">{{ $charge->charge_type_label }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Description</span>
                    <span class="font-medium text-secondary">{{ $charge->description }}</span>
                </div>
            @else
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Room</span>
                    <span class="font-medium text-secondary">{{ $booking->room->room_number ?? 'N/A' }} — {{ $booking->room->roomType->name ?? '' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Stay</span>
                    <span class="font-medium text-secondary">{{ $booking->check_in_date->format('M d') }} → {{ $booking->check_out_date->format('M d, Y') }} ({{ $booking->nights }} nights)</span>
                </div>
            @endif
            <div class="flex justify-between text-sm pt-3 border-t border-gray-100">
                <span class="font-bold text-secondary">Amount to Pay</span>
                <span class="text-xl font-bold text-primary">@currency($amount, 'TZS')</span>
            </div>
        </div>
    </div>

    <!-- Payment Form -->
    <form method="POST" action="{{ route('payments.store', $booking) }}" 
          x-data="{ method: 'mobile', submitting: false }"
          @submit="submitting = true"
          class="space-y-6">
        @csrf

        <input type="hidden" name="amount" value="{{ $amount }}">
        @if($charge)
            <input type="hidden" name="charge_id" value="{{ $charge->id }}">
        @endif

        <!-- Payment Method Selection -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Select Payment Method</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @if(in_array('mobile', $methods))
                <label class="relative cursor-pointer">
                    <input type="radio" name="payment_method" value="mobile" x-model="method" class="peer sr-only">
                    <div class="border-2 border-gray-200 rounded-xl p-4 text-center transition-all
                                peer-checked:border-primary peer-checked:bg-blue-50">
                        <div class="w-12 h-12 mx-auto mb-2 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-secondary">Mobile Money</span>
                        <p class="text-xs text-gray-500 mt-1">M-Pesa, Tigo Pesa, Airtel Money</p>
                    </div>
                </label>
                @endif

                @if(in_array('card', $methods))
                <label class="relative cursor-pointer">
                    <input type="radio" name="payment_method" value="card" x-model="method" class="peer sr-only">
                    <div class="border-2 border-gray-200 rounded-xl p-4 text-center transition-all
                                peer-checked:border-primary peer-checked:bg-blue-50">
                        <div class="w-12 h-12 mx-auto mb-2 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-secondary">Card</span>
                        <p class="text-xs text-gray-500 mt-1">Visa, Mastercard</p>
                    </div>
                </label>
                @endif

                @if(in_array('dynamic-qr', $methods))
                <label class="relative cursor-pointer">
                    <input type="radio" name="payment_method" value="dynamic-qr" x-model="method" class="peer sr-only">
                    <div class="border-2 border-gray-200 rounded-xl p-4 text-center transition-all
                                peer-checked:border-primary peer-checked:bg-blue-50">
                        <div class="w-12 h-12 mx-auto mb-2 bg-purple-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-secondary">QR Code</span>
                        <p class="text-xs text-gray-500 mt-1">Scan & Pay</p>
                    </div>
                </label>
                @endif
            </div>
            @error('payment_method')
                <p class="text-sm text-red-600 mt-2">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone Number (for Mobile Money) -->
        <div x-show="method === 'mobile'" x-transition class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Mobile Money Details</h3>
            <div>
                <label for="phone_number" class="block text-sm font-semibold text-secondary mb-2">Phone Number</label>
                <input type="text" name="phone_number" id="phone_number"
                       value="{{ old('phone_number', $booking->guest_display_phone) }}"
                       placeholder="e.g. 255712345678"
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all text-sm">
                <p class="text-xs text-gray-500 mt-1">Enter the mobile number registered with your mobile money account (with country code).</p>
                @error('phone_number')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Submit -->
        <div class="flex justify-end">
            <button type="submit" 
                    :disabled="submitting"
                    class="inline-flex items-center gap-2 px-8 py-3 bg-primary text-white text-sm font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                <template x-if="!submitting">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Pay @currency($amount, 'TZS')
                    </span>
                </template>
                <template x-if="submitting">
                    <span class="flex items-center gap-2">
                        <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Processing...
                    </span>
                </template>
            </button>
        </div>
    </form>
</div>
@endsection
