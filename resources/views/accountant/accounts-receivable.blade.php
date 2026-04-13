@extends('layouts.app')

@section('title', __('accountant.sidebar.accounts_receivable'))
@section('page-title', __('accountant.sidebar.accounts_receivable'))

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white p-6 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.accounts_receivable') }}</div><div class="mt-2 text-3xl font-extrabold text-sky-600"><x-money :amount="$totalReceivables" /></div></div>
    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-gray-100 text-left text-gray-500"><th class="pb-3">{{ __('accountant.table.reference') }}</th><th class="pb-3">{{ __('general.name') }}</th><th class="pb-3">{{ __('general.date') }}</th><th class="pb-3">{{ __('general.status') }}</th><th class="pb-3 text-right">{{ __('general.amount') }}</th></tr></thead>
                <tbody>
                    @forelse($receivables as $invoice)
                        <tr class="border-b border-gray-50 last:border-0"><td class="py-3 font-semibold text-secondary">{{ $invoice->invoice_no }}</td><td class="py-3 text-gray-600">{{ $invoice->guest_name }}</td><td class="py-3 text-gray-600">{{ $invoice->invoice_date?->format('M d, Y') }}</td><td class="py-3 text-gray-600">{{ ucfirst($invoice->status) }}</td><td class="py-3 text-right font-bold text-sky-700"><x-money :amount="$invoice->total" /></td></tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
