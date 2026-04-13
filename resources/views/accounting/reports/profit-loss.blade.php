@extends('layouts.app')

@section('title', __('accountant.reports.profit_loss'))
@section('page-title', __('accountant.reports.profit_loss'))

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.total_revenue') }}</div><div class="mt-2 text-2xl font-extrabold text-emerald-600"><x-money :amount="$totalRevenue" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.total_expenses') }}</div><div class="mt-2 text-2xl font-extrabold text-rose-600"><x-money :amount="$totalExpenses + $totalCogs" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.net_profit') }}</div><div class="mt-2 text-2xl font-extrabold text-sky-600"><x-money :amount="$netProfit" /></div></div>
    </div>
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl bg-white p-6 shadow-sm"><h3 class="text-lg font-extrabold text-secondary">Revenue</h3><div class="mt-4 space-y-3">@foreach($revenueAccounts as $row)<div class="flex items-center justify-between"><span>{{ $row['account']->name }}</span><span class="font-bold"><x-money :amount="$row['balance']" /></span></div>@endforeach</div></div>
        <div class="rounded-2xl bg-white p-6 shadow-sm"><h3 class="text-lg font-extrabold text-secondary">COGS</h3><div class="mt-4 space-y-3">@foreach($cogsAccounts as $row)<div class="flex items-center justify-between"><span>{{ $row['account']->name }}</span><span class="font-bold"><x-money :amount="$row['balance']" /></span></div>@endforeach</div></div>
        <div class="rounded-2xl bg-white p-6 shadow-sm"><h3 class="text-lg font-extrabold text-secondary">Expenses</h3><div class="mt-4 space-y-3">@foreach($expenseAccounts as $row)<div class="flex items-center justify-between"><span>{{ $row['account']->name }}</span><span class="font-bold"><x-money :amount="$row['balance']" /></span></div>@endforeach</div></div>
    </div>
</div>
@endsection
