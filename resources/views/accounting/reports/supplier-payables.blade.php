@extends('layouts.app')

@section('title', 'Supplier Payables')
@section('page-title', 'Supplier Payables')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Supplier Payables</h2>
            <p class="mt-1 text-sm text-gray-500">Track supplier liabilities from procurement postings and payments</p>
        </div>
        <form method="GET" class="flex flex-col gap-3 sm:flex-row">
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="rounded-lg border border-gray-300 px-4 py-2.5">
            <input type="date" name="date_to" value="{{ $dateTo }}" class="rounded-lg border border-gray-300 px-4 py-2.5">
            <button type="submit" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Apply</button>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-lg">
            <div class="text-sm text-gray-500">Outstanding Payables</div>
            <div class="mt-2 text-3xl font-extrabold text-red-600"><x-money :amount="$totalOutstanding" /></div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-lg">
            <div class="text-sm text-gray-500">Supplier Invoices Posted</div>
            <div class="mt-2 text-3xl font-extrabold text-secondary"><x-money :amount="$totalInvoiced" /></div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-lg">
            <div class="text-sm text-gray-500">Payments Applied</div>
            <div class="mt-2 text-3xl font-extrabold text-green-600"><x-money :amount="$totalPaid" /></div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white shadow-lg overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-4">
            <h3 class="text-lg font-bold text-secondary">Supplier Balances</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-primary">Supplier</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-primary">Invoiced</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-primary">Paid</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-primary">Outstanding</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-primary">Recent Entries</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($supplierRows as $row)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-secondary">{{ $row['supplier']->name }}</div>
                        <div class="text-xs text-gray-500">{{ $row['supplier']->phone ?: ($row['supplier']->email ?: 'No contact info') }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm font-semibold text-secondary"><x-money :amount="$row['invoiced']" /></td>
                    <td class="px-6 py-4 text-sm font-semibold text-green-600"><x-money :amount="$row['paid']" /></td>
                    <td class="px-6 py-4 text-sm font-bold {{ $row['balance'] > 0 ? 'text-red-600' : 'text-green-600' }}"><x-money :amount="$row['balance']" /></td>
                    <td class="px-6 py-4">
                        <div class="space-y-1">
                            @foreach($row['entries']->take(3) as $entry)
                            <a href="{{ route('accounting.journal.show', $entry) }}" class="block text-xs font-semibold text-primary hover:text-blue-700">
                                {{ $entry->entry_no }} - {{ $entry->reference ?? 'No ref' }}
                            </a>
                            @endforeach
                            @if($row['entries']->isEmpty())
                            <span class="text-xs text-gray-400">No entries</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-sm text-gray-500">No supplier payable activity found in this period.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="rounded-2xl border border-gray-100 bg-white shadow-lg overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-4">
            <h3 class="text-lg font-bold text-secondary">Recent Procurement Entries</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-primary">Entry</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-primary">Supplier</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-primary">Reference</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-primary">Amount</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-primary">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($recentProcurementEntries as $entry)
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-secondary">{{ $entry->entry_no }}</div>
                        <div class="text-xs text-gray-500">{{ $entry->entry_date->format('M d, Y') }}</div>
                    </td>
                    <td class="px-6 py-4 text-sm text-secondary">{{ $entry->supplier?->name ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm text-secondary">{{ $entry->reference ?? '—' }}</td>
                    <td class="px-6 py-4 text-sm font-bold text-secondary"><x-money :amount="$entry->total_debit" /></td>
                    <td class="px-6 py-4 text-sm">
                        <a href="{{ route('accounting.journal.show', $entry) }}" class="font-semibold text-primary hover:text-blue-700">Open Entry</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-sm text-gray-500">No procurement journal entries found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
