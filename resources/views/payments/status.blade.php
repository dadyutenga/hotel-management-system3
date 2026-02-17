{{-- resources/views/payments/status.blade.php --}}
@extends('layouts.app')

@section('title', 'Payment Status')
@section('page-title', 'Payments')

@section('content')
<div class="max-w-xl mx-auto space-y-6" x-data="paymentStatus()" x-init="startPolling()">
    <!-- Header -->
    <div class="text-center">
        <h2 class="text-2xl font-extrabold text-secondary">Payment Status</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $payment->payment_number }}</p>
    </div>

    <!-- Status Card -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
        <!-- Pending State -->
        <template x-if="status === 'pending'">
            <div class="space-y-6">
                <div class="w-20 h-20 mx-auto bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-yellow-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-secondary">Waiting for Payment</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        @if($payment->payment_method === 'mobile')
                            A USSD prompt has been sent to your phone. Please complete the payment on your device.
                        @elseif($payment->payment_method === 'dynamic-qr')
                            Scan the QR code below with your mobile money app to complete the payment.
                        @else
                            Your payment is being processed. Please wait...
                        @endif
                    </p>
                </div>

                @if($payment->payment_qr_code)
                    <div class="border border-gray-200 rounded-xl p-4 inline-block mx-auto">
                        <img src="{{ $payment->payment_qr_code }}" alt="Payment QR Code" class="w-48 h-48 mx-auto">
                    </div>
                @endif

                @if($payment->payment_method === 'mobile')
                    <button @click="resendPush()" 
                            :disabled="pushCooldown > 0"
                            class="px-6 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="pushCooldown <= 0">Resend Payment Prompt</span>
                        <span x-show="pushCooldown > 0">Resend in <span x-text="pushCooldown"></span>s</span>
                    </button>
                @endif

                <div class="flex items-center justify-center gap-2 text-xs text-gray-400">
                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Checking status automatically...
                </div>
            </div>
        </template>

        <!-- Success State -->
        <template x-if="status === 'successful'">
            <div class="space-y-6">
                <div class="w-20 h-20 mx-auto bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-green-700">Payment Successful!</h3>
                    <p class="text-sm text-gray-500 mt-2">Your payment of <strong>TZS {{ number_format($payment->amount, 0) }}</strong> has been confirmed.</p>
                </div>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('payments.show', $payment) }}" 
                       class="px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all">
                        View Receipt
                    </a>
                    <a href="{{ route('bookings.show', $payment->booking) }}" 
                       class="px-6 py-2.5 border border-gray-200 text-secondary text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
                        Back to Booking
                    </a>
                </div>
            </div>
        </template>

        <!-- Failed State -->
        <template x-if="status === 'failed'">
            <div class="space-y-6">
                <div class="w-20 h-20 mx-auto bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-red-700">Payment Failed</h3>
                    <p class="text-sm text-gray-500 mt-2">The payment could not be processed. Please try again.</p>
                </div>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('payments.create', $payment->booking) }}" 
                       class="px-6 py-2.5 bg-primary text-white text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all">
                        Try Again
                    </a>
                    <a href="{{ route('bookings.show', $payment->booking) }}" 
                       class="px-6 py-2.5 border border-gray-200 text-secondary text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
                        Back to Booking
                    </a>
                </div>
            </div>
        </template>
    </div>

    <!-- Payment Details -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Payment Details</h3>
        <div class="space-y-3">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Payment #</span>
                <span class="font-medium text-secondary">{{ $payment->payment_number }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Booking</span>
                <span class="font-medium text-secondary">{{ $payment->booking->booking_number }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Method</span>
                <span class="font-medium text-secondary capitalize">{{ str_replace('-', ' ', $payment->payment_method) }}</span>
            </div>
            <div class="flex justify-between text-sm pt-2 border-t border-gray-100">
                <span class="font-bold text-secondary">Amount</span>
                <span class="text-lg font-bold text-primary">TZS {{ number_format($payment->amount, 0) }}</span>
            </div>
        </div>
    </div>
</div>

<script>
function paymentStatus() {
    return {
        status: '{{ $payment->status }}',
        pollInterval: null,
        pushCooldown: 0,

        startPolling() {
            if (this.status !== 'pending') return;
            
            this.pollInterval = setInterval(() => {
                this.checkStatus();
            }, 5000); // Poll every 5 seconds
        },

        async checkStatus() {
            try {
                const response = await fetch('{{ route("payments.check-status", $payment) }}');
                const data = await response.json();
                
                if (data.status !== 'pending') {
                    this.status = data.status;
                    clearInterval(this.pollInterval);
                }
            } catch (e) {
                console.error('Status check failed:', e);
            }
        },

        async resendPush() {
            if (this.pushCooldown > 0) return;
            
            try {
                const response = await fetch('{{ route("payments.trigger-push", $payment) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    },
                });
                const data = await response.json();
                
                if (data.success) {
                    this.pushCooldown = 30;
                    const timer = setInterval(() => {
                        this.pushCooldown--;
                        if (this.pushCooldown <= 0) clearInterval(timer);
                    }, 1000);
                }
            } catch (e) {
                console.error('Push trigger failed:', e);
            }
        }
    };
}
</script>
@endsection
