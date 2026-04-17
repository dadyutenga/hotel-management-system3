@extends('layouts.app')

@section('title', __('accountant.receipts.detail_title'))
@section('page-title', __('accountant.receipts.detail_title'))

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-secondary">{{ $receipt->receipt_number }}</h2>
                <p class="mt-1 text-sm text-gray-500">{{ __('accountant.receipts.issued_at') }}: {{ optional($receipt->issued_at)->format('M d, Y H:i') }}</p>
                <p class="mt-1 text-sm text-gray-500">{{ __('accountant.receipts.module') }}: {{ __('accountant.receipts.module_' . $receipt->module) }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('receipts.show', $receipt->uuid) }}" target="_blank" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white">
                    {{ __('accountant.receipts.print') }}
                </a>
                <a href="{{ route('receipts.reprint', $receipt->receipt_number) }}" target="_blank" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700">
                    {{ __('accountant.receipts.reprint') }}
                </a>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-gray-500">{{ __('general.total') }}</div>
            <div class="mt-2 text-2xl font-extrabold text-secondary"><x-money :amount="$receipt->total" /> {{ $receipt->currency }}</div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-gray-500">{{ __('accountant.receipts.amount_paid') }}</div>
            <div class="mt-2 text-2xl font-extrabold text-emerald-600"><x-money :amount="$receipt->amount_paid" /> {{ $receipt->currency }}</div>
        </div>
        <div class="rounded-2xl bg-white p-5 shadow-sm">
            <div class="text-sm text-gray-500">{{ __('accountant.receipts.balance') }}</div>
            <div class="mt-2 text-2xl font-extrabold text-amber-600"><x-money :amount="$receipt->balance" /> {{ $receipt->currency }}</div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl bg-white p-6 shadow-sm space-y-3">
            <h3 class="text-lg font-bold text-secondary">{{ __('accountant.receipts.source_linkage') }}</h3>

            <div class="text-sm">
                <span class="text-gray-500">{{ __('accountant.receipts.source_type') }}:</span>
                <span class="font-medium text-secondary">{{ class_basename((string) $receipt->receiptable_type) ?: '—' }}</span>
            </div>
            <div class="text-sm">
                <span class="text-gray-500">{{ __('accountant.receipts.source_id') }}:</span>
                <span class="font-medium text-secondary">{{ $receipt->receiptable_id ?: '—' }}</span>
            </div>

            @if($sourceLink)
                <div class="text-sm">
                    <span class="text-gray-500">{{ $sourceLink['label'] }}:</span>
                    <a href="{{ $sourceLink['url'] }}" class="font-semibold text-indigo-600 hover:text-indigo-700" target="_blank">
                        {{ $sourceLink['reference'] }}
                    </a>
                </div>
            @else
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-3 text-sm text-amber-700">
                    {{ __('accountant.receipts.source_missing') }}
                </div>
            @endif

            @if($financeLink)
                <div class="text-sm">
                    <span class="text-gray-500">{{ $financeLink['label'] }}:</span>
                    @if(!empty($financeLink['url']))
                        <a href="{{ $financeLink['url'] }}" class="font-semibold text-indigo-600 hover:text-indigo-700" target="_blank">{{ $financeLink['reference'] }}</a>
                    @else
                        <span class="font-medium text-secondary">{{ $financeLink['reference'] }}</span>
                    @endif
                </div>
            @endif
        </div>

        <div class="rounded-2xl bg-white p-6 shadow-sm space-y-3">
            <h3 class="text-lg font-bold text-secondary">{{ __('accountant.receipts.meta') }}</h3>
            <div class="text-sm"><span class="text-gray-500">{{ __('accountant.receipts.customer') }}:</span> <span class="font-medium text-secondary">{{ $receipt->customer_name ?: '—' }}</span></div>
            <div class="text-sm"><span class="text-gray-500">{{ __('general.phone') }}:</span> <span class="font-medium text-secondary">{{ $receipt->customer_phone ?: '—' }}</span></div>
            <div class="text-sm"><span class="text-gray-500">{{ __('accountant.receipts.payment_method') }}:</span> <span class="font-medium text-secondary">{{ __('accountant.receipts.payment_method_' . ($receipt->payment_method ?: 'unknown')) }}</span></div>
            <div class="text-sm"><span class="text-gray-500">{{ __('general.status') }}:</span> <span class="font-medium text-secondary">{{ __('accountant.receipts.status_' . $receipt->payment_status) }}</span></div>
            <div class="text-sm"><span class="text-gray-500">{{ __('accountant.receipts.cashier') }}:</span> <span class="font-medium text-secondary">{{ $receipt->cashier_name ?: ($receipt->cashier?->name ?: '—') }}</span></div>
            <div class="text-sm"><span class="text-gray-500">{{ __('accountant.receipts.transaction_reference') }}:</span> <span class="font-medium text-secondary">{{ $receipt->transaction_reference ?: '—' }}</span></div>
        </div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <h3 class="text-lg font-bold text-secondary">{{ __('accountant.receipts.line_items') }}</h3>
        <div class="mt-4 overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left text-gray-500">
                        <th class="pb-3">{{ __('general.description') }}</th>
                        <th class="pb-3 text-right">{{ __('general.quantity') }}</th>
                        <th class="pb-3 text-right">{{ __('general.price') }}</th>
                        <th class="pb-3 text-right">{{ __('general.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($receipt->items_snapshot ?? []) as $item)
                        <tr class="border-b border-gray-50 last:border-0">
                            <td class="py-3">
                                <div class="font-semibold text-secondary">{{ $item['name'] ?? $item['description'] ?? 'Item' }}</div>
                                @if(!empty($item['details']))
                                    <div class="text-xs text-gray-500">{{ $item['details'] }}</div>
                                @endif
                            </td>
                            <td class="py-3 text-right">{{ $item['quantity'] ?? 1 }}</td>
                            <td class="py-3 text-right"><x-money :amount="($item['unit_price'] ?? 0)" /></td>
                            <td class="py-3 text-right"><x-money :amount="($item['amount'] ?? (($item['quantity'] ?? 1) * ($item['unit_price'] ?? 0)))" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

