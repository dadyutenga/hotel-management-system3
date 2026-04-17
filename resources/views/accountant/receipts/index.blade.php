@extends('layouts.app')

@section('title', __('accountant.receipts.title'))
@section('page-title', __('accountant.receipts.title'))

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-gray-500">{{ __('accountant.receipts.total_receipts_today') }}</div>
            <div class="mt-2 text-2xl font-extrabold text-secondary">{{ number_format($summary['total_receipts_today']) }}</div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-gray-500">{{ __('accountant.receipts.total_amount_today') }}</div>
            <div class="mt-2 text-2xl font-extrabold text-emerald-600"><x-money :amount="$summary['total_amount_today']" /></div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-gray-500">{{ __('accountant.receipts.payment_method_split') }}</div>
            <div class="mt-3 space-y-2">
                @forelse($summary['by_payment_method'] as $row)
                    <div class="flex items-center justify-between text-sm">
                        <span class="font-medium text-secondary">{{ __('accountant.receipts.payment_method_' . ($row->payment_method ?? 'unknown')) }}</span>
                        <span class="text-gray-600">{{ number_format((int) $row->receipt_count) }} / <x-money :amount="$row->total_amount" /></span>
                    </div>
                @empty
                    <div class="text-sm text-gray-500">{{ __('general.no_data') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    <form method="GET" class="grid gap-4 rounded-2xl bg-white p-6 shadow-sm md:grid-cols-4 xl:grid-cols-7">
        <input type="date" name="date_from" value="{{ $filters['date_from'] }}" class="rounded-xl border-gray-200 text-sm" placeholder="{{ __('accountant.receipts.date_from') }}">
        <input type="date" name="date_to" value="{{ $filters['date_to'] }}" class="rounded-xl border-gray-200 text-sm" placeholder="{{ __('accountant.receipts.date_to') }}">

        <select name="module" class="rounded-xl border-gray-200 text-sm">
            <option value="">{{ __('accountant.receipts.all_modules') }}</option>
            @foreach($modules as $module)
                <option value="{{ $module }}" @selected($filters['module'] === $module)>{{ __('accountant.receipts.module_' . $module) }}</option>
            @endforeach
        </select>

        <input type="text" name="receipt_number" value="{{ $filters['receipt_number'] }}" class="rounded-xl border-gray-200 text-sm" placeholder="{{ __('accountant.receipts.receipt_no') }}">

        <select name="payment_method" class="rounded-xl border-gray-200 text-sm">
            <option value="">{{ __('accountant.receipts.all_payment_methods') }}</option>
            @foreach($paymentMethods as $method)
                <option value="{{ $method }}" @selected($filters['payment_method'] === $method)>{{ __('accountant.receipts.payment_method_' . $method) }}</option>
            @endforeach
        </select>

        <select name="status" class="rounded-xl border-gray-200 text-sm">
            <option value="">{{ __('accountant.receipts.all_statuses') }}</option>
            @foreach($statuses as $status)
                <option value="{{ $status }}" @selected($filters['status'] === $status)>{{ __('accountant.receipts.status_' . $status) }}</option>
            @endforeach
        </select>

        <div class="flex gap-2">
            <button class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">{{ __('general.filter') }}</button>
            <a href="{{ route('accountant.receipts.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700">{{ __('general.reset') }}</a>
        </div>

        <input type="text" name="q" value="{{ $filters['q'] }}" class="rounded-xl border-gray-200 text-sm md:col-span-4 xl:col-span-7" placeholder="{{ __('accountant.receipts.search_customer') }}">
    </form>

    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left text-gray-500">
                        <th class="pb-3">{{ __('accountant.receipts.receipt_no') }}</th>
                        <th class="pb-3">{{ __('accountant.receipts.module') }}</th>
                        <th class="pb-3">{{ __('accountant.receipts.issued_at') }}</th>
                        <th class="pb-3">{{ __('accountant.receipts.customer') }}</th>
                        <th class="pb-3 text-right">{{ __('general.amount') }}</th>
                        <th class="pb-3">{{ __('accountant.receipts.payment_method') }}</th>
                        <th class="pb-3">{{ __('general.status') }}</th>
                        <th class="pb-3">{{ __('accountant.receipts.cashier') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipts as $receipt)
                        <tr class="border-b border-gray-50 last:border-0">
                            <td class="py-3">
                                <a href="{{ route('accountant.receipts.show', $receipt) }}" class="font-semibold text-indigo-600 hover:text-indigo-700">
                                    {{ $receipt->receipt_number }}
                                </a>
                            </td>
                            <td class="py-3 text-gray-700">{{ __('accountant.receipts.module_' . $receipt->module) }}</td>
                            <td class="py-3 text-gray-600">{{ optional($receipt->issued_at)->format('M d, Y H:i') }}</td>
                            <td class="py-3 text-gray-700">{{ $receipt->customer_name ?: '—' }}</td>
                            <td class="py-3 text-right font-semibold"><x-money :amount="$receipt->total" /> {{ $receipt->currency }}</td>
                            <td class="py-3 text-gray-700">{{ __('accountant.receipts.payment_method_' . ($receipt->payment_method ?: 'unknown')) }}</td>
                            <td class="py-3">
                                <span class="rounded-full px-2 py-1 text-xs font-semibold bg-gray-100 text-gray-700">
                                    {{ __('accountant.receipts.status_' . $receipt->payment_status) }}
                                </span>
                            </td>
                            <td class="py-3 text-gray-700">{{ $receipt->cashier_name ?: ($receipt->cashier?->name ?: '—') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $receipts->links() }}</div>
    </div>
</div>
@endsection

