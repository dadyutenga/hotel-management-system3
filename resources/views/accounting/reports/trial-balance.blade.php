@extends('layouts.app')

@section('title', __('accountant.reports.trial_balance'))
@section('page-title', __('accountant.reports.trial_balance'))

@section('content')
<div class="rounded-2xl bg-white p-6 shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-gray-100 text-left text-gray-500"><th class="pb-3">Code</th><th class="pb-3">Account</th><th class="pb-3 text-right">Debit</th><th class="pb-3 text-right">Credit</th></tr></thead>
            <tbody>
                @foreach($accounts as $row)
                    <tr class="border-b border-gray-50 last:border-0"><td class="py-3 font-semibold">{{ $row['account']->code }}</td><td class="py-3">{{ $row['account']->name }}</td><td class="py-3 text-right"><x-money :amount="$row['debit']" /></td><td class="py-3 text-right"><x-money :amount="$row['credit']" /></td></tr>
                @endforeach
            </tbody>
            <tfoot><tr class="border-t border-gray-200 font-extrabold"><td colspan="2" class="pt-3">Totals</td><td class="pt-3 text-right"><x-money :amount="$totalDebits" /></td><td class="pt-3 text-right"><x-money :amount="$totalCredits" /></td></tr></tfoot>
        </table>
    </div>
</div>
@endsection
