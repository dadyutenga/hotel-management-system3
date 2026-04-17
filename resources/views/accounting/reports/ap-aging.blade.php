@extends('layouts.app')

@section('title', __('accountant.reports.ap_aging'))
@section('page-title', __('accountant.reports.ap_aging'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ __('accountant.reports.ap_aging') }}</h2>
            <p class="text-sm text-gray-500">{{ __('accountant.report_center.filter_as_of') }}</p>
        </div>
        <form method="GET" class="flex flex-col gap-3 sm:flex-row">
            <input type="date" name="as_of" value="{{ $asOf }}" class="rounded-lg border border-gray-300 px-4 py-2.5">
            <button type="submit" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">{{ __('accountant.report_center.apply') }}</button>
        </form>
    </div>

    <div class="grid gap-4 md:grid-cols-5">
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.report_center.bucket_0_30') }}</div><div class="mt-2 text-xl font-extrabold text-secondary"><x-money :amount="$totals['current']" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.report_center.bucket_31_60') }}</div><div class="mt-2 text-xl font-extrabold text-secondary"><x-money :amount="$totals['days_31_60']" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.report_center.bucket_61_90') }}</div><div class="mt-2 text-xl font-extrabold text-secondary"><x-money :amount="$totals['days_61_90']" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.report_center.bucket_90_plus') }}</div><div class="mt-2 text-xl font-extrabold text-secondary"><x-money :amount="$totals['days_90_plus']" /></div></div>
        <div class="rounded-2xl bg-white p-5 shadow-sm"><div class="text-sm text-gray-500">{{ __('accountant.report_center.total_outstanding') }}</div><div class="mt-2 text-xl font-extrabold text-rose-600"><x-money :amount="$totals['total']" /></div></div>
    </div>

    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left text-gray-500">
                        <th class="pb-3">{{ __('accountant.report_center.supplier') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.report_center.bucket_0_30') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.report_center.bucket_31_60') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.report_center.bucket_61_90') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.report_center.bucket_90_plus') }}</th>
                        <th class="pb-3 text-right">{{ __('accountant.report_center.total_outstanding') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supplierAging as $row)
                        <tr class="border-b border-gray-50 last:border-0">
                            <td class="py-3 font-semibold">{{ $row['supplier']?->name ?? '—' }}</td>
                            <td class="py-3 text-right"><x-money :amount="$row['current']" /></td>
                            <td class="py-3 text-right"><x-money :amount="$row['days_31_60']" /></td>
                            <td class="py-3 text-right"><x-money :amount="$row['days_61_90']" /></td>
                            <td class="py-3 text-right"><x-money :amount="$row['days_90_plus']" /></td>
                            <td class="py-3 text-right font-semibold"><x-money :amount="$row['total']" /></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-6 text-center text-gray-500">{{ __('general.no_data') }}</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="border-t border-gray-200 font-extrabold">
                        <td class="pt-3">{{ __('accountant.report_center.totals') }}</td>
                        <td class="pt-3 text-right"><x-money :amount="$totals['current']" /></td>
                        <td class="pt-3 text-right"><x-money :amount="$totals['days_31_60']" /></td>
                        <td class="pt-3 text-right"><x-money :amount="$totals['days_61_90']" /></td>
                        <td class="pt-3 text-right"><x-money :amount="$totals['days_90_plus']" /></td>
                        <td class="pt-3 text-right"><x-money :amount="$totals['total']" /></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
