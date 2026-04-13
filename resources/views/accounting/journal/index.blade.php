@extends('layouts.app')

@section('title', 'Journal Entries')
@section('page-title', 'Journal Entries')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Journal Entries</h2>
            <p class="text-sm text-gray-500 mt-1">Track accounting postings across procurement and finance</p>
        </div>
        @if(auth()->user()->hasRole('ACCOUNTANT'))
        <a href="{{ route('accounting.journal.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Entry
        </a>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-sm text-gray-500">Posted Entries</div>
            <div class="mt-2 text-3xl font-extrabold text-secondary">{{ $entries->total() }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-sm text-gray-500">Procurement Entries</div>
            <div class="mt-2 text-3xl font-extrabold text-green-600">{{ $entries->where('source', 'procurement')->count() }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-sm text-gray-500">Supplier-linked</div>
            <div class="mt-2 text-3xl font-extrabold text-primary">{{ $entries->whereNotNull('supplier_id')->count() }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-sm text-gray-500">Total Value</div>
            <div class="mt-2 text-2xl font-extrabold text-secondary"><x-money :amount="$entries->sum('total_debit')" /></div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Entry</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Source</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Reference</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Actions</th>
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
                        <div class="text-sm font-semibold text-secondary">{{ ucwords(str_replace('_', ' ', $entry->source)) }}</div>
                        <div class="text-xs text-gray-500">{{ $entry->description }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="text-sm text-secondary">{{ $entry->supplier?->name ?? '—' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-secondary">{{ $entry->reference ?? '—' }}</div>
                        @if($entry->source === 'procurement' && $entry->reference && str_starts_with($entry->reference, 'GRN-'))
                        <a href="{{ route('procurement.grn.show', $entry->source_id) }}" class="text-xs text-primary hover:text-blue-700">Open GRN</a>
                        @else
                        <div class="text-xs text-gray-500">Source ID: {{ $entry->source_id ?? '—' }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-bold text-secondary"><x-money :amount="$entry->total_debit" /></div>
                        <div class="text-xs text-gray-500">{{ $entry->lines->count() }} lines</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $entry->status === 'posted' ? 'bg-green-100 text-green-700' : ($entry->status === 'reversed' ? 'bg-red-100 text-red-600' : 'bg-yellow-100 text-yellow-700') }}">{{ ucfirst($entry->status) }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('accounting.journal.show', $entry) }}" class="text-primary hover:text-blue-700 font-semibold">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center text-gray-500">No journal entries found.</td>
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
