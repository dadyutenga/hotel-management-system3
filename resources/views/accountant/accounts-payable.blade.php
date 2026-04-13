@extends('layouts.app')

@section('title', __('accountant.sidebar.accounts_payable'))
@section('page-title', __('accountant.sidebar.accounts_payable'))

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white p-6 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.accounts_payable') }}</div><div class="mt-2 text-3xl font-extrabold text-amber-600"><x-money :amount="$totalOutstanding" /></div></div>
    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-gray-100 text-left text-gray-500"><th class="pb-3">{{ __('general.name') }}</th><th class="pb-3 text-right">{{ __('accountant.labels.invoiced') }}</th><th class="pb-3 text-right">{{ __('accountant.labels.paid') }}</th><th class="pb-3 text-right">{{ __('accountant.labels.balance') }}</th></tr></thead>
                <tbody>
                    @forelse($payables as $row)
                        <tr class="border-b border-gray-50 last:border-0"><td class="py-3 font-semibold text-secondary">{{ $row['supplier']->name }}</td><td class="py-3 text-right text-gray-600"><x-money :amount="$row['invoiced']" /></td><td class="py-3 text-right text-gray-600"><x-money :amount="$row['paid']" /></td><td class="py-3 text-right font-bold text-amber-700"><x-money :amount="$row['balance']" /></td></tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.sections.bank_reconciliation') }}</h3>
        <div class="mt-4 space-y-3">
            @forelse($recentReconciliations as $reconciliation)
                <div class="flex items-center justify-between rounded-xl bg-gray-50 px-4 py-3"><div><div class="font-semibold text-secondary">{{ $reconciliation->reference_no }}</div><div class="text-xs text-gray-500">{{ $reconciliation->account?->name }}</div></div><div class="text-right"><div class="font-bold text-secondary"><x-money :amount="$reconciliation->difference" /></div><div class="text-xs text-gray-500">{{ ucfirst($reconciliation->status) }}</div></div></div>
            @empty
                <div class="text-sm text-gray-500">{{ __('general.no_data') }}</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
