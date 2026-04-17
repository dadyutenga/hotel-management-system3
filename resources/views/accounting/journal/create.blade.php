@extends('layouts.app')

@php
    $isEdit = isset($journalEntry) && $journalEntry !== null;
    $lineInputs = old('lines', $isEdit
        ? $journalEntry->lines->map(fn ($line) => [
            'account_id' => $line->account_id,
            'type' => $line->type,
            'amount' => $line->amount,
            'notes' => $line->notes,
        ])->all()
        : array_fill(0, 4, ['account_id' => '', 'type' => 'debit', 'amount' => '', 'notes' => '']));
@endphp

@section('title', $isEdit ? __('accountant.journal.titles.edit') : __('accountant.journal.titles.create'))
@section('page-title', $isEdit ? __('accountant.journal.titles.edit') : __('accountant.journal.titles.create'))

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-extrabold text-secondary">
            {{ $isEdit ? __('accountant.journal.titles.edit') : __('accountant.journal.titles.create_manual') }}
        </h2>
        <p class="text-sm text-gray-500 mt-1">{{ __('accountant.journal.labels.balanced_hint') }}</p>
    </div>

    <form method="POST" action="{{ $isEdit ? route('accounting.journal.update', $journalEntry) : route('accounting.journal.store') }}" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 space-y-6" id="journal-form">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('accountant.journal.labels.entry_date') }}</label>
                <input type="date" name="entry_date" value="{{ old('entry_date', $isEdit ? $journalEntry->entry_date->toDateString() : now()->toDateString()) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg @error('entry_date') border-red-500 @enderror" required>
                @error('entry_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('accountant.journal.labels.reference') }}</label>
                <input type="text" name="reference" value="{{ old('reference', $isEdit ? $journalEntry->reference : '') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg @error('reference') border-red-500 @enderror">
                @error('reference')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('accountant.journal.labels.description') }}</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg @error('description') border-red-500 @enderror" required>{{ old('description', $isEdit ? $journalEntry->description : '') }}</textarea>
                @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-secondary">{{ __('accountant.journal.labels.lines') }}</h3>
                <span class="text-xs text-gray-500">{{ __('accountant.journal.labels.lines_hint') }}</span>
            </div>
            @error('lines')<p class="mb-3 text-sm text-red-600">{{ $message }}</p>@enderror
            <div class="space-y-4">
                @foreach($lineInputs as $index => $line)
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                    <div class="md:col-span-5">
                        <select name="lines[{{ $index }}][account_id]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg line-account">
                            <option value="">{{ __('accountant.journal.labels.select_account') }}</option>
                            @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ ($line['account_id'] ?? '') === $account->id ? 'selected' : '' }}>{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <select name="lines[{{ $index }}][type]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg line-type">
                            <option value="debit" {{ ($line['type'] ?? 'debit') === 'debit' ? 'selected' : '' }}>{{ __('accountant.journal.labels.debit') }}</option>
                            <option value="credit" {{ ($line['type'] ?? '') === 'credit' ? 'selected' : '' }}>{{ __('accountant.journal.labels.credit') }}</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <input type="number" step="0.01" min="0.01" name="lines[{{ $index }}][amount]" value="{{ $line['amount'] ?? '' }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg line-amount" placeholder="{{ __('accountant.journal.labels.amount') }}">
                    </div>
                    <div class="md:col-span-3">
                        <input type="text" name="lines[{{ $index }}][notes]" value="{{ $line['notes'] ?? '' }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg" placeholder="{{ __('accountant.journal.labels.notes') }}">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
            <div class="rounded-xl bg-emerald-50 px-4 py-3 font-semibold text-emerald-700">
                {{ __('accountant.journal.labels.total_debit') }}: <span id="total-debit">0.00</span>
            </div>
            <div class="rounded-xl bg-blue-50 px-4 py-3 font-semibold text-blue-700">
                {{ __('accountant.journal.labels.total_credit') }}: <span id="total-credit">0.00</span>
            </div>
            <div class="rounded-xl bg-gray-100 px-4 py-3 font-semibold text-gray-700">
                {{ __('accountant.journal.labels.balance_status') }}:
                <span id="balance-state">{{ __('accountant.journal.labels.unbalanced') }}</span>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('accounting.journal.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">{{ __('general.cancel') }}</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-primary to-blue-600 rounded-lg hover:shadow-lg">
                {{ $isEdit ? __('accountant.journal.actions.update_draft') : __('accountant.journal.actions.save_draft') }}
            </button>
        </div>
    </form>
</div>

<script>
    (function () {
        const form = document.getElementById('journal-form');
        if (!form) return;

        const debitTarget = document.getElementById('total-debit');
        const creditTarget = document.getElementById('total-credit');
        const balanceState = document.getElementById('balance-state');

        function recompute() {
            let debit = 0;
            let credit = 0;

            form.querySelectorAll('.grid').forEach(() => {});
            const rows = form.querySelectorAll('.line-amount');
            rows.forEach((amountInput) => {
                const wrapper = amountInput.closest('.grid');
                const typeInput = wrapper ? wrapper.querySelector('.line-type') : null;
                const accountInput = wrapper ? wrapper.querySelector('.line-account') : null;
                const amount = parseFloat(amountInput.value || '0');

                if (!typeInput || !accountInput || !accountInput.value || Number.isNaN(amount) || amount <= 0) {
                    return;
                }

                if (typeInput.value === 'debit') debit += amount;
                if (typeInput.value === 'credit') credit += amount;
            });

            debitTarget.textContent = debit.toFixed(2);
            creditTarget.textContent = credit.toFixed(2);
            balanceState.textContent = Math.abs(debit - credit) < 0.01
                ? @json(__('accountant.journal.labels.balanced'))
                : @json(__('accountant.journal.labels.unbalanced'));
        }

        form.addEventListener('input', recompute);
        recompute();
    })();
</script>
@endsection
