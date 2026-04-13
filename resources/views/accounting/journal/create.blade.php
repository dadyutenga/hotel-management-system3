@extends('layouts.app')

@section('title', 'Create Journal Entry')
@section('page-title', 'Create Journal Entry')

@section('content')
@php
    $lineInputs = old('lines', array_fill(0, 4, ['account_id' => '', 'type' => 'debit', 'amount' => '', 'notes' => '']));
@endphp
<div class="max-w-5xl mx-auto space-y-6">
    <div>
        <h2 class="text-2xl font-extrabold text-secondary">Create Manual Journal Entry</h2>
        <p class="text-sm text-gray-500 mt-1">Add a balanced manual accounting posting</p>
    </div>

    <form method="POST" action="{{ route('accounting.journal.store') }}" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Entry Date</label>
                <input type="date" name="entry_date" value="{{ old('entry_date', now()->toDateString()) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg @error('entry_date') border-red-500 @enderror" required>
                @error('entry_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Reference</label>
                <input type="text" name="reference" value="{{ old('reference') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg @error('reference') border-red-500 @enderror">
                @error('reference')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg @error('description') border-red-500 @enderror" required>{{ old('description') }}</textarea>
                @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-secondary">Journal Lines</h3>
                <span class="text-xs text-gray-500">Fill at least one debit and one credit line</span>
            </div>
            @error('lines')<p class="mb-3 text-sm text-red-600">{{ $message }}</p>@enderror
            <div class="space-y-4">
                @foreach($lineInputs as $index => $line)
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                    <div class="md:col-span-5">
                        <select name="lines[{{ $index }}][account_id]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                            <option value="">Select account</option>
                            @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ ($line['account_id'] ?? '') === $account->id ? 'selected' : '' }}>{{ $account->code }} - {{ $account->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <select name="lines[{{ $index }}][type]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
                            <option value="debit" {{ ($line['type'] ?? 'debit') === 'debit' ? 'selected' : '' }}>Debit</option>
                            <option value="credit" {{ ($line['type'] ?? '') === 'credit' ? 'selected' : '' }}>Credit</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <input type="number" step="0.01" min="0.01" name="lines[{{ $index }}][amount]" value="{{ $line['amount'] ?? '' }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg" placeholder="Amount">
                    </div>
                    <div class="md:col-span-3">
                        <input type="text" name="lines[{{ $index }}][notes]" value="{{ $line['notes'] ?? '' }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg" placeholder="Notes">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('accounting.journal.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-primary to-blue-600 rounded-lg hover:shadow-lg">Post Entry</button>
        </div>
    </form>
</div>
@endsection
