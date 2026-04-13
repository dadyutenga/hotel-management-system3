@extends('layouts.app')

@section('title', __('accountant.reports.balance_sheet'))
@section('page-title', __('accountant.reports.balance_sheet'))

@section('content')
<div class="grid gap-6 lg:grid-cols-3">
    <div class="rounded-2xl bg-white p-6 shadow-sm"><h3 class="text-lg font-extrabold text-secondary">Assets</h3><div class="mt-4 space-y-3">@foreach($assets as $row)<div class="flex items-center justify-between"><span>{{ $row['account']->name }}</span><span class="font-bold"><x-money :amount="$row['balance']" /></span></div>@endforeach<div class="border-t border-gray-100 pt-3 font-extrabold">Total: <x-money :amount="$totalAssets" /></div></div></div>
    <div class="rounded-2xl bg-white p-6 shadow-sm"><h3 class="text-lg font-extrabold text-secondary">Liabilities</h3><div class="mt-4 space-y-3">@foreach($liabilities as $row)<div class="flex items-center justify-between"><span>{{ $row['account']->name }}</span><span class="font-bold"><x-money :amount="$row['balance']" /></span></div>@endforeach<div class="border-t border-gray-100 pt-3 font-extrabold">Total: <x-money :amount="$totalLiabilities" /></div></div></div>
    <div class="rounded-2xl bg-white p-6 shadow-sm"><h3 class="text-lg font-extrabold text-secondary">Equity</h3><div class="mt-4 space-y-3">@foreach($equity as $row)<div class="flex items-center justify-between"><span>{{ $row['account']->name }}</span><span class="font-bold"><x-money :amount="$row['balance']" /></span></div>@endforeach<div class="border-t border-gray-100 pt-3 font-extrabold">Total: <x-money :amount="$totalEquity" /></div></div></div>
</div>
@endsection
