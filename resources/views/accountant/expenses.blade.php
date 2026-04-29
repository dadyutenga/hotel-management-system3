@extends('layouts.app')

@section('title', __('accountant.sidebar.expense_management'))
@section('page-title', __('accountant.sidebar.expense_management'))

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl bg-white p-5 shadow-sm xl:col-span-2">
            <div class="text-sm text-gray-500">{{ __('accountant.labels.supplier_payments_settled') }}</div>
            <div class="mt-2 text-3xl font-extrabold text-emerald-600"><x-money :amount="$supplierPaymentsSettled" /></div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm xl:col-span-2">
            <div class="text-sm text-gray-500">{{ __('accountant.labels.outstanding_supplier_payables') }}</div>
            <div class="mt-2 text-3xl font-extrabold text-amber-600"><x-money :amount="$outstandingSupplierPayables" /></div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="text-xl font-extrabold text-secondary">{{ __('accountant.sections.expense_accounts') }}</h2>
            <div class="mt-4 space-y-3">
                @forelse($expenseAccounts as $row)
                    <div class="flex items-center justify-between rounded-xl bg-gray-50 px-4 py-3"><div><div class="font-semibold text-secondary">{{ $row['account']->code }} - {{ $row['account']->name }}</div><div class="text-xs text-gray-500">{{ ucfirst($row['account']->type) }}</div></div><div class="font-bold text-rose-600"><x-money :amount="$row['balance']" /></div></div>
                @empty
                    <div class="text-sm text-gray-500">{{ __('general.no_data') }}</div>
                @endforelse
            </div>
        </div>
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <h2 class="text-xl font-extrabold text-secondary">{{ __('accountant.sections.recent_expense_activity') }}</h2>
            <div class="mt-4 space-y-3">
                @forelse($recentExpenseLines as $line)
                    <div class="rounded-xl bg-rose-50 px-4 py-3"><div class="flex items-center justify-between gap-3"><div><div class="font-semibold text-secondary">{{ $line->account?->name }}</div><div class="text-xs text-gray-500">{{ $line->entry?->entry_no }} - {{ $line->entry?->entry_date?->format('M d, Y') }}</div></div><div class="font-bold text-rose-600"><x-money :amount="$line->amount" /></div></div></div>
                @empty
                    <div class="text-sm text-gray-500">{{ __('general.no_data') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="text-xl font-extrabold text-secondary">{{ __('accountant.sections.supplier_payables_context') }}</h2>
        <p class="mt-1 text-sm text-gray-600">{{ __('accountant.sections.recent_supplier_payments') }}</p>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full min-w-[760px] text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left text-gray-500">
                        <th class="pb-3">{{ __('general.name') }}</th>
                        <th class="pb-3">{{ __('accountant.ap.payment_reference') }}</th>
                        <th class="pb-3">{{ __('general.date') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.labels.allocated_total') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.labels.posted_amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSupplierPayments as $payment)
                        <tr class="border-b border-gray-50 last:border-0">
                            <td class="py-3 font-semibold text-secondary">{{ $payment->supplier?->name ?: '-' }}</td>
                            <td class="py-3 text-gray-700">{{ $payment->reference ?: $payment->id }}</td>
                            <td class="py-3 text-gray-600">{{ $payment->posted_at?->format('M d, Y') ?: $payment->payment_date?->format('M d, Y') }}</td>
                            <td class="py-3 text-right font-semibold text-indigo-700"><x-money :amount="$payment->allocations->sum('allocated_amount')" /></td>
                            <td class="py-3 text-right font-bold text-emerald-700"><x-money :amount="$payment->amount" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
