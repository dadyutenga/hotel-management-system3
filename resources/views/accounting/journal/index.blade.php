@extends('layouts.app')

@section('title', __('accountant.journal.titles.index'))
@section('page-title', __('accountant.journal.titles.index'))

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ __('accountant.journal.titles.index') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('accountant.journal.labels.index_subtitle') }}</p>
        </div>
        @if(auth()->user()->hasRole('ACCOUNTANT'))
        <a href="{{ route('accounting.journal.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('accountant.journal.actions.new_entry') }}
        </a>
        @endif
    </div>

    <form method="GET" action="{{ route('accounting.journal.index') }}" class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 grid grid-cols-1 md:grid-cols-5 gap-3">
        <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
            <option value="">{{ __('accountant.journal.filters.all_statuses') }}</option>
            @foreach(['draft', 'posted', 'reversed'] as $status)
                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ __('accountant.journal.statuses.' . $status) }}</option>
            @endforeach
        </select>
        <select name="source" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg">
            <option value="">{{ __('accountant.journal.filters.all_sources') }}</option>
            @foreach(['manual', 'procurement', 'petty_cash', 'booking', 'restaurant', 'laundry', 'payroll'] as $source)
                <option value="{{ $source }}" @selected(($filters['source'] ?? '') === $source)>{{ __('accountant.journal.sources.' . $source) }}</option>
            @endforeach
        </select>
        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg" />
        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg" />
        <button class="px-5 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-primary to-blue-600 rounded-lg hover:shadow-lg">{{ __('general.filter') }}</button>
    </form>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-sm text-gray-500">{{ __('accountant.journal.labels.total_entries') }}</div>
            <div class="mt-2 text-3xl font-extrabold text-secondary">{{ (int) ($stats->total_entries ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-sm text-gray-500">{{ __('accountant.journal.labels.posted_entries') }}</div>
            <div class="mt-2 text-3xl font-extrabold text-green-600">{{ (int) ($stats->posted_entries ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-sm text-gray-500">{{ __('accountant.journal.labels.manual_entries') }}</div>
            <div class="mt-2 text-3xl font-extrabold text-primary">{{ (int) ($stats->manual_entries ?? 0) }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-sm text-gray-500">{{ __('accountant.journal.labels.total_value') }}</div>
            <div class="mt-2 text-2xl font-extrabold text-secondary"><x-money :amount="$stats->total_value ?? 0" /></div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('accountant.journal.labels.entry') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('accountant.journal.labels.source') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('accountant.journal.labels.supplier') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('accountant.journal.labels.reference') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('accountant.journal.labels.amount') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('accountant.journal.labels.status') }}</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('accountant.journal.labels.actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($entries as $entry)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-secondary">{{ $entry->entry_no }}</div>
                        <div class="text-xs text-gray-500">{{ $entry->entry_date->format('M d, Y') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-secondary">{{ __('accountant.journal.sources.' . $entry->source) }}</div>
                        <div class="text-xs text-gray-500">{{ $entry->description }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-secondary">{{ $entry->supplier?->name ?? '—' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-secondary">{{ $entry->reference ?? '—' }}</div>
                        @if($entry->source === 'procurement' && $entry->reference && str_starts_with($entry->reference, 'GRN-'))
                        <a href="{{ route('procurement.grn.show', $entry->source_id) }}" class="text-xs text-primary hover:text-blue-700">{{ __('accountant.journal.actions.open_grn') }}</a>
                        @else
                        <div class="text-xs text-gray-500">{{ __('accountant.journal.labels.source_id') }}: {{ $entry->source_id ?? '—' }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-secondary"><x-money :amount="$entry->total_debit" /></div>
                        <div class="text-xs text-gray-500">{{ $entry->lines->count() }} {{ __('accountant.journal.labels.lines_count') }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $entry->status === 'posted' ? 'bg-green-100 text-green-700' : ($entry->status === 'reversed' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700') }}">{{ __('accountant.journal.statuses.' . $entry->status) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('accounting.journal.show', $entry) }}" class="text-primary hover:text-blue-700 font-semibold">{{ __('general.view') }}</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-gray-500">{{ __('accountant.journal.messages.empty') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($entries->hasPages())
    <div>{{ $entries->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
