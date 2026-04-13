@extends('layouts.app')

@section('title', __('accountant.sidebar.financial_overview'))
@section('page-title', __('accountant.sidebar.financial_overview'))

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.cash_position') }}</div><div class="mt-2 text-3xl font-extrabold text-emerald-600"><x-money :amount="$snapshot['cashPosition']" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.accounts_payable') }}</div><div class="mt-2 text-3xl font-extrabold text-amber-600"><x-money :amount="$snapshot['accountsPayable']" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.accounts_receivable') }}</div><div class="mt-2 text-3xl font-extrabold text-sky-600"><x-money :amount="$snapshot['accountsReceivable']" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.open_payrolls') }}</div><div class="mt-2 text-3xl font-extrabold text-indigo-600">{{ $snapshot['openPayrollRuns'] }}</div></div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.sections.month_to_date') }}</h3>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="rounded-xl bg-emerald-50 p-4"><div class="text-sm text-emerald-700">{{ __('accountant.metrics.month_revenue') }}</div><div class="mt-2 text-2xl font-extrabold text-emerald-600"><x-money :amount="$monthlyTrend['revenue']" /></div></div>
                <div class="rounded-xl bg-rose-50 p-4"><div class="text-sm text-rose-700">{{ __('accountant.metrics.month_expenses') }}</div><div class="mt-2 text-2xl font-extrabold text-rose-600"><x-money :amount="$monthlyTrend['expenses']" /></div></div>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.sections.recent_payrolls') }}</h3>
            <div class="mt-4 space-y-3">
                @forelse($monthlyTrend['payrolls'] as $payroll)
                    <div class="flex items-center justify-between rounded-xl bg-gray-50 px-4 py-3"><div><div class="font-semibold text-secondary">{{ $payroll->reference_no }}</div><div class="text-xs text-gray-500">{{ $payroll->pay_date?->format('M d, Y') }}</div></div><div class="font-bold text-secondary"><x-money :amount="$payroll->total_net" /></div></div>
                @empty
                    <div class="text-sm text-gray-500">{{ __('general.no_data') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.sections.bank_reconciliation') }}</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-gray-100 text-left text-gray-500"><th class="pb-3">{{ __('accountant.table.reference') }}</th><th class="pb-3">{{ __('general.date') }}</th><th class="pb-3">{{ __('general.status') }}</th><th class="pb-3 text-right">{{ __('accountant.labels.difference') }}</th></tr></thead>
                <tbody>
                    @forelse($monthlyTrend['bank_reconciliations'] as $reconciliation)
                        <tr class="border-b border-gray-50 last:border-0"><td class="py-3 font-semibold text-secondary">{{ $reconciliation->reference_no }}</td><td class="py-3 text-gray-600">{{ $reconciliation->statement_date?->format('M d, Y') }}</td><td class="py-3 text-gray-600">{{ ucfirst($reconciliation->status) }}</td><td class="py-3 text-right font-bold text-secondary"><x-money :amount="$reconciliation->difference" /></td></tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
