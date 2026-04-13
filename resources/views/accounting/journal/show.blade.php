@extends('layouts.app')

@section('title', $journalEntry->entry_no)
@section('page-title', 'Journal Entry Details')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $journalEntry->entry_no }}</h2>
            <p class="text-sm text-gray-500 mt-1">Accounting posting details and source links</p>
        </div>
        <a href="{{ route('accounting.journal.index') }}" class="text-primary hover:text-blue-700 font-semibold">← Back to Journal</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-sm text-gray-500">Source</div>
            <div class="mt-2 text-xl font-extrabold text-secondary">{{ ucwords(str_replace('_', ' ', $journalEntry->source)) }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-sm text-gray-500">Supplier</div>
            <div class="mt-2 text-xl font-extrabold text-secondary">{{ $journalEntry->supplier?->name ?? '—' }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-sm text-gray-500">Debit</div>
            <div class="mt-2 text-xl font-extrabold text-green-600"><x-money :amount="$journalEntry->total_debit" /></div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
            <div class="text-sm text-gray-500">Credit</div>
            <div class="mt-2 text-xl font-extrabold text-primary"><x-money :amount="$journalEntry->total_credit" /></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Entry Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-gray-500">Reference</div>
                        <div class="mt-1 font-semibold text-secondary">{{ $journalEntry->reference ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Entry Date</div>
                        <div class="mt-1 font-semibold text-secondary">{{ $journalEntry->entry_date->format('M d, Y') }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Created By</div>
                        <div class="mt-1 font-semibold text-secondary">{{ $journalEntry->creator?->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">Posted By</div>
                        <div class="mt-1 font-semibold text-secondary">{{ $journalEntry->poster?->name ?? '—' }}</div>
                    </div>
                    <div class="md:col-span-2">
                        <div class="text-gray-500">Description</div>
                        <div class="mt-1 font-semibold text-secondary">{{ $journalEntry->description }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-secondary">Journal Lines</h3>
                </div>
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gradient-to-r from-blue-50 to-white">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Account</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Notes</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-primary uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($journalEntry->lines as $line)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-secondary">{{ $line->account->code }} - {{ $line->account->name }}</div>
                                <div class="text-xs text-gray-500">{{ ucfirst($line->account->type) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $line->type === 'debit' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">{{ ucfirst($line->type) }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $line->notes ?? '—' }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-secondary"><x-money :amount="$line->amount" /></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Procurement Context</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Supplier</span>
                        <span class="font-semibold text-secondary">{{ $journalEntry->supplier?->name ?? '—' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Source Record</span>
                        <span class="font-semibold text-secondary">{{ $journalEntry->source_id ?? '—' }}</span>
                    </div>
                    @if($journalEntry->source === 'procurement' && $journalEntry->reference && str_starts_with($journalEntry->reference, 'GRN-'))
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">GRN</span>
                        <a href="{{ route('procurement.grn.show', $journalEntry->source_id) }}" class="font-semibold text-primary hover:text-blue-700">{{ $journalEntry->reference }}</a>
                    </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Status</span>
                        <span class="font-semibold {{ $journalEntry->status === 'posted' ? 'text-green-600' : 'text-amber-600' }}">{{ ucfirst($journalEntry->status) }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Balance Check</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Total Debit</span>
                        <span class="font-semibold text-secondary"><x-money :amount="$journalEntry->total_debit" /></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Total Credit</span>
                        <span class="font-semibold text-secondary"><x-money :amount="$journalEntry->total_credit" /></span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-700">
                        <span>Balanced</span>
                        <span>{{ $journalEntry->isBalanced() ? 'Yes' : 'No' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
