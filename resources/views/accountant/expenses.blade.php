@extends('layouts.app')

@section('title', __('accountant.sidebar.expense_management'))
@section('page-title', __('accountant.sidebar.expense_management'))

@section('content')
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
@endsection
