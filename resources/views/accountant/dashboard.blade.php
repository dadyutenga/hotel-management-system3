@extends('layouts.app')

@section('title', __('accountant.dashboard_title'))
@section('page-title', __('accountant.dashboard_title'))

@section('content')
<div class="space-y-6">
    <div class="rounded-3xl bg-gradient-to-r from-slate-900 via-emerald-800 to-teal-700 p-6 text-white shadow-xl">
        <h2 class="text-2xl font-extrabold">{{ __('accountant.hero.title') }}</h2>
        <p class="mt-2 text-sm text-emerald-100">{{ __('accountant.hero.subtitle') }}</p>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-2xl border border-emerald-100 bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.total_revenue') }}</div><div class="mt-2 text-3xl font-extrabold text-emerald-600"><x-money :amount="$snapshot['totalRevenue']" /></div></div>
        <div class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.total_expenses') }}</div><div class="mt-2 text-3xl font-extrabold text-rose-600"><x-money :amount="$snapshot['totalExpenses']" /></div></div>
        <div class="rounded-2xl border border-sky-100 bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.net_profit') }}</div><div class="mt-2 text-3xl font-extrabold text-sky-600"><x-money :amount="$snapshot['netProfit']" /></div></div>
        <div class="rounded-2xl border border-amber-100 bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.pending_payments') }}</div><div class="mt-2 text-3xl font-extrabold text-amber-600">{{ $snapshot['pendingInvoices'] }}</div></div>
    </div>

    <div class="grid gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between"><h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.sections.recent_transactions') }}</h3><a href="{{ route('accountant.transactions') }}" class="text-sm font-semibold text-indigo-600">{{ __('general.view_all') }}</a></div>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-gray-100 text-left text-gray-500"><th class="pb-3">{{ __('accountant.table.reference') }}</th><th class="pb-3">{{ __('general.description') }}</th><th class="pb-3">{{ __('general.status') }}</th><th class="pb-3 text-right">{{ __('general.amount') }}</th></tr></thead>
                    <tbody>
                        @forelse($recentTransactions as $transaction)
                            <tr class="border-b border-gray-50 last:border-0">
                                <td class="py-3 font-semibold text-secondary">{{ $transaction->entry_no }}</td>
                                <td class="py-3 text-gray-600">{{ $transaction->description }}</td>
                                <td class="py-3"><span class="rounded-full bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700">{{ ucfirst($transaction->status) }}</span></td>
                                <td class="py-3 text-right font-bold text-secondary"><x-money :amount="$transaction->total_debit" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.sections.quick_insights') }}</h3>
            <div class="mt-4 space-y-3">
                @foreach($insights as $insight)
                    <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                        <div class="text-sm text-gray-500">{{ $insight['label'] }}</div>
                        <div class="mt-1 text-xl font-extrabold {{ $insight['tone'] === 'negative' ? 'text-rose-600' : ($insight['tone'] === 'warning' ? 'text-amber-600' : 'text-emerald-600') }}"><x-money :amount="$insight['value']" /></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between"><h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.sections.accounts_payable') }}</h3><a href="{{ route('accountant.payables.dashboard') }}" class="text-sm font-semibold text-indigo-600">{{ __('general.view_all') }}</a></div>
            <div class="mt-4 space-y-3">
                @forelse($pendingPayables as $row)
                    <div class="flex items-center justify-between rounded-xl bg-amber-50 px-4 py-3">
                        <div><div class="font-semibold text-secondary">{{ $row['supplier']->name }}</div><div class="text-xs text-gray-500">{{ __('accountant.labels.invoiced') }}: <x-money :amount="$row['invoiced']" /></div></div>
                        <div class="text-right font-bold text-amber-700"><x-money :amount="$row['balance']" /></div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-200 px-4 py-6 text-center text-gray-500">{{ __('general.no_data') }}</div>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between"><h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.sections.accounts_receivable') }}</h3><a href="{{ route('accountant.accounts-receivable') }}" class="text-sm font-semibold text-indigo-600">{{ __('general.view_all') }}</a></div>
            <div class="mt-4 space-y-3">
                @forelse($pendingReceivables as $invoice)
                    <div class="flex items-center justify-between rounded-xl bg-sky-50 px-4 py-3">
                        <div><div class="font-semibold text-secondary">{{ $invoice->invoice_no }}</div><div class="text-xs text-gray-500">{{ $invoice->guest_name }}</div></div>
                        <div class="text-right"><div class="font-bold text-sky-700"><x-money :amount="$invoice->total" /></div><div class="text-xs text-gray-500">{{ ucfirst($invoice->status) }}</div></div>
                    </div>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-200 px-4 py-6 text-center text-gray-500">{{ __('general.no_data') }}</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
