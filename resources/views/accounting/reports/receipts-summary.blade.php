@extends('layouts.app')

@section('title', __('accountant.reports.receipts_summary'))
@section('page-title', __('accountant.reports.receipts_summary'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ __('accountant.reports.receipts_summary') }}</h2>
            <p class="text-sm text-gray-500">{{ __('accountant.report_center.filter_period') }}</p>
        </div>
        <form method="GET" class="flex flex-col gap-3 sm:flex-row">
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="rounded-lg border border-gray-300 px-4 py-2.5">
            <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded-lg border border-gray-300 px-4 py-2.5">
            <button type="submit" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">{{ __('accountant.report_center.apply') }}</button>
        </form>
    </div>

    <div class="grid gap-4 md:grid-cols-4">
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.report_center.receipt_count') }}</div><div class="mt-2 text-2xl font-extrabold text-secondary">{{ $totals['count'] }}</div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.report_center.gross_total') }}</div><div class="mt-2 text-2xl font-extrabold text-emerald-600"><x-money :amount="$totals['gross_total']" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.report_center.amount_paid') }}</div><div class="mt-2 text-2xl font-extrabold text-sky-600"><x-money :amount="$totals['amount_paid']" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.report_center.balance') }}</div><div class="mt-2 text-2xl font-extrabold text-rose-600"><x-money :amount="$totals['balance']" /></div></div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.report_center.receipts_by_module') }}</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-left text-gray-500">
                            <th class="pb-3">{{ __('accountant.report_center.module') }}</th>
                            <th class="pb-3 text-right">{{ __('accountant.report_center.receipt_count') }}</th>
                            <th class="pb-3 text-right">{{ __('accountant.report_center.gross_total') }}</th>
                            <th class="pb-3 text-right">{{ __('accountant.report_center.amount_paid') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byModule as $row)
                            <tr class="border-b border-gray-50 last:border-0">
                                <td class="py-3 capitalize">{{ $row['module'] }}</td>
                                <td class="py-3 text-right">{{ $row['count'] }}</td>
                                <td class="py-3 text-right"><x-money :amount="$row['total']" /></td>
                                <td class="py-3 text-right"><x-money :amount="$row['amount_paid']" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm">
            <h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.report_center.receipts_by_method') }}</h3>
            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-left text-gray-500">
                            <th class="pb-3">{{ __('accountant.report_center.payment_method') }}</th>
                            <th class="pb-3 text-right">{{ __('accountant.report_center.receipt_count') }}</th>
                            <th class="pb-3 text-right">{{ __('accountant.report_center.gross_total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byPaymentMethod as $row)
                            <tr class="border-b border-gray-50 last:border-0">
                                <td class="py-3">{{ $row['method'] }}</td>
                                <td class="py-3 text-right">{{ $row['count'] }}</td>
                                <td class="py-3 text-right"><x-money :amount="$row['total']" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.reports.receipts_summary') }}</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left text-gray-500">
                        <th class="pb-3">{{ __('accountant.report_center.receipt_number') }}</th>
                        <th class="pb-3">{{ __('accountant.report_center.date') }}</th>
                        <th class="pb-3">{{ __('accountant.report_center.module') }}</th>
                        <th class="pb-3">{{ __('accountant.report_center.customer') }}</th>
                        <th class="pb-3">{{ __('accountant.report_center.payment_method') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.report_center.gross_total') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.report_center.amount_paid') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.report_center.balance') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                        <tr class="border-b border-gray-50 last:border-0">
                            <td class="py-3 font-semibold">{{ $receipt->receipt_number }}</td>
                            <td class="py-3">{{ optional($receipt->issued_at)->format('M d, Y') }}</td>
                            <td class="py-3 capitalize">{{ $receipt->module }}</td>
                            <td class="py-3">{{ $receipt->customer_name ?: '—' }}</td>
                            <td class="py-3">{{ $receipt->payment_method ?: '—' }}</td>
                            <td class="py-3 text-right"><x-money :amount="$receipt->total" /></td>
                            <td class="py-3 text-right"><x-money :amount="$receipt->amount_paid" /></td>
                            <td class="py-3 text-right"><x-money :amount="$receipt->balance" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
