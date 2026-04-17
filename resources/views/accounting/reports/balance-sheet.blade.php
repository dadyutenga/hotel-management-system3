@extends('layouts.app')

@section('title', __('accountant.reports.balance_sheet'))
@section('page-title', __('accountant.reports.balance_sheet'))

@section('content')
<div class="mb-6 flex justify-end">
    <form method="GET" class="flex flex-col gap-3 sm:flex-row">
        <input type="date" name="as_of" value="{{ $asOf }}" class="rounded-lg border border-gray-300 px-4 py-2.5">
        <button type="submit" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">{{ __('accountant.report_center.apply') }}</button>
    </form>
</div>
<div class="grid gap-6 lg:grid-cols-3">
    <div class="rounded-2xl bg-white p-6 shadow-sm"><h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.report_center.assets') }}</h3><div class="mt-4 space-y-3">@foreach($assets as $row)<div class="flex items-center justify-between"><span>{{ $row['account']->name }}</span><span class="font-bold"><x-money :amount="$row['balance']" /></span></div>@endforeach<div class="border-t border-gray-100 pt-3 font-extrabold">{{ __('accountant.report_center.totals') }}: <x-money :amount="$totalAssets" /></div></div></div>
    <div class="rounded-2xl bg-white p-6 shadow-sm"><h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.report_center.liabilities') }}</h3><div class="mt-4 space-y-3">@foreach($liabilities as $row)<div class="flex items-center justify-between"><span>{{ $row['account']->name }}</span><span class="font-bold"><x-money :amount="$row['balance']" /></span></div>@endforeach<div class="border-t border-gray-100 pt-3 font-extrabold">{{ __('accountant.report_center.totals') }}: <x-money :amount="$totalLiabilities" /></div></div></div>
    <div class="rounded-2xl bg-white p-6 shadow-sm"><h3 class="text-lg font-extrabold text-secondary">{{ __('accountant.report_center.equity') }}</h3><div class="mt-4 space-y-3">@foreach($equity as $row)<div class="flex items-center justify-between"><span>{{ $row['account']->name }}</span><span class="font-bold"><x-money :amount="$row['balance']" /></span></div>@endforeach<div class="border-t border-gray-100 pt-3 font-extrabold">{{ __('accountant.report_center.totals') }}: <x-money :amount="$totalEquity" /></div></div></div>
</div>
@endsection
