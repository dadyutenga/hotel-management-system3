@extends('layouts.app')

@section('title', __('accountant.ap.create_payment_title'))
@section('page-title', __('accountant.ap.create_payment_title'))

@section('content')
<form method="POST" action="{{ route('accountant.payments.store') }}" class="mx-auto max-w-3xl space-y-6 rounded-2xl bg-white p-6 shadow-sm">
    @csrf
    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('general.name') }}</label>
            <select name="supplier_id" class="w-full rounded-xl border-gray-200 text-sm" required>
                <option value="">{{ __('accountant.ap.select_supplier') }}</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" @selected(old('supplier_id') === $supplier->id)>{{ $supplier->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('general.date') }}</label>
            <input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" class="w-full rounded-xl border-gray-200 text-sm" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('general.amount') }}</label>
            <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" class="w-full rounded-xl border-gray-200 text-sm" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('accountant.ap.payment_method') }}</label>
            <select name="method" class="w-full rounded-xl border-gray-200 text-sm" required>
                @foreach(['cash', 'bank', 'mobile', 'card'] as $method)
                    <option value="{{ $method }}" @selected(old('method') === $method)>{{ ucfirst($method) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('accountant.ap.currency') }}</label>
            <select name="currency" class="w-full rounded-xl border-gray-200 text-sm" required>
                @foreach(['USD', 'TZS'] as $currency)
                    <option value="{{ $currency }}" @selected(old('currency', \App\Helpers\CurrencyHelper::getDefaultCurrency()) === $currency)>{{ $currency }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('accountant.ap.payment_reference') }}</label>
            <input type="text" name="reference" value="{{ old('reference') }}" class="w-full rounded-xl border-gray-200 text-sm">
        </div>
    </div>

    <div>
        <label class="mb-2 block text-sm font-semibold text-gray-700">{{ __('general.notes') }}</label>
        <textarea name="notes" rows="4" class="w-full rounded-xl border-gray-200 text-sm">{{ old('notes') }}</textarea>
    </div>

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('accountant.payables.dashboard') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700">{{ __('general.cancel') }}</a>
        <button class="rounded-xl bg-indigo-600 px-5 py-2 text-sm font-semibold text-white">{{ __('accountant.ap.save_draft') }}</button>
    </div>
</form>
@endsection
