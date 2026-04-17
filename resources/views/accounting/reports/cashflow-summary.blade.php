@extends('layouts.app')

@section('title', __('accountant.reports.cashflow_summary'))
@section('page-title', __('accountant.reports.cashflow_summary'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ __('accountant.reports.cashflow_summary') }}</h2>
            <p class="text-sm text-gray-500">{{ __('accountant.report_center.filter_period') }}</p>
        </div>
        <form method="GET" class="flex flex-col gap-3 sm:flex-row">
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="rounded-lg border border-gray-300 px-4 py-2.5">
            <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded-lg border border-gray-300 px-4 py-2.5">
            <button type="submit" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">{{ __('accountant.report_center.apply') }}</button>
        </form>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-gray-500">{{ __('accountant.report_center.cash_inflow') }}</div>
            <div class="mt-2 text-2xl font-extrabold text-emerald-600"><x-money :amount="$totalInflow" /></div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-gray-500">{{ __('accountant.report_center.cash_outflow') }}</div>
            <div class="mt-2 text-2xl font-extrabold text-rose-600"><x-money :amount="$totalOutflow" /></div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-gray-500">{{ __('accountant.report_center.net_cash_movement') }}</div>
            <div class="mt-2 text-2xl font-extrabold text-sky-600"><x-money :amount="$netCashMovement" /></div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.report_center.cash_accounts_breakdown') }}</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left text-gray-500">
                        <th class="pb-3">{{ __('accountant.report_center.account') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.report_center.inflow') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.report_center.outflow') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.report_center.net') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($accountSummaries as $row)
                        <tr class="border-b border-gray-50 last:border-0">
                            <td class="py-3 font-semibold">{{ $row['account']->name }}</td>
                            <td class="py-3 text-right"><x-money :amount="$row['inflow']" /></td>
                            <td class="py-3 text-right"><x-money :amount="$row['outflow']" /></td>
                            <td class="py-3 text-right"><x-money :amount="$row['net']" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.report_center.cashflow_entries') }}</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left text-gray-500">
                        <th class="pb-3">{{ __('accountant.report_center.date') }}</th>
                        <th class="pb-3">{{ __('accountant.report_center.account') }}</th>
                        <th class="pb-3">{{ __('accountant.report_center.reference') }}</th>
                        <th class="pb-3">{{ __('accountant.report_center.description') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.report_center.inflow') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.report_center.outflow') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr class="border-b border-gray-50 last:border-0">
                            <td class="py-3">{{ optional($row['entry']?->entry_date)->format('M d, Y') }}</td>
                            <td class="py-3">{{ $row['account']?->name }}</td>
                            <td class="py-3">{{ $row['reference'] ?: '—' }}</td>
                            <td class="py-3">{{ $row['description'] ?: '—' }}</td>
                            <td class="py-3 text-right"><x-money :amount="$row['inflow']" /></td>
                            <td class="py-3 text-right"><x-money :amount="$row['outflow']" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
