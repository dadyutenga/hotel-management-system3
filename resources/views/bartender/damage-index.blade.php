@extends('layouts.app')

@section('title', __('bartender.titles.damage_index'))
@section('page-title', __('bartender.titles.damage_index'))

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-800">{{ __('bartender.messages.damage_audit') }}</h2>
        <a href="{{ route('bartender.damage.create') }}" class="px-3 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700">{{ __('bartender.actions.report_damage') }}</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.date_time') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.item') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('bartender.fields.qty') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.reason') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.notes') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.reported_by') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reports as $report)
                    <tr>
                        <td class="px-4 py-3 text-gray-500">{{ $report->reported_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $report->product->name }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format((float)$report->quantity, 3) }}</td>
                        <td class="px-4 py-3">{{ __('bartender.reasons.' . $report->reason) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $report->notes }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $report->reporter->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">{{ __('bartender.messages.no_damage') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $reports->links() }}</div>
</div>
@endsection
