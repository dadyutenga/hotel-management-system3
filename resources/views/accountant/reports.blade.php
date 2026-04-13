@extends('layouts.app')

@section('title', __('accountant.sidebar.reports'))
@section('page-title', __('accountant.sidebar.reports'))

@section('content')
<div class="space-y-6">
    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.total_revenue') }}</div><div class="mt-2 text-2xl font-extrabold text-emerald-600"><x-money :amount="$snapshot['totalRevenue']" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.total_expenses') }}</div><div class="mt-2 text-2xl font-extrabold text-rose-600"><x-money :amount="$snapshot['totalExpenses']" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.metrics.net_profit') }}</div><div class="mt-2 text-2xl font-extrabold text-sky-600"><x-money :amount="$snapshot['netProfit']" /></div></div>
    </div>
    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="text-xl font-extrabold text-secondary">{{ __('accountant.sections.financial_reports') }}</h2>
        <div class="mt-4 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach($reportLinks as $report)
                <a href="{{ route($report['route']) }}" class="rounded-2xl border border-gray-100 bg-gray-50 p-5 transition hover:border-indigo-200 hover:bg-indigo-50"><div class="text-lg font-bold text-secondary">{{ $report['label'] }}</div><div class="mt-2 text-sm text-gray-500">{{ __('accountant.labels.open_report') }}</div></a>
            @endforeach
        </div>
    </div>
</div>
@endsection
