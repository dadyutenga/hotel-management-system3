@extends('layouts.app')

@section('title', 'GRN Approval Dashboard')
@section('page-title', 'GRN Approval Dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">GRN Approval Dashboard</h2>
            <p class="mt-1 text-sm text-gray-500">Review submitted and confirmed GRNs before final approval</p>
        </div>

        <form method="GET" class="grid w-full grid-cols-1 gap-3 sm:grid-cols-2 xl:max-w-5xl xl:grid-cols-6">
            <select name="status" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-secondary focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                <option value="">Pending Manager Action</option>
                <option value="{{ \App\Models\GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER }}" @selected(request('status') === \App\Models\GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER)>Confirmed by Storekeeper</option>
                <option value="{{ \App\Models\GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL }}" @selected(request('status') === \App\Models\GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL)>Pending Manager Approval</option>
                <option value="{{ \App\Models\GoodsReceivedNote::STATUS_APPROVED }}" @selected(request('status') === \App\Models\GoodsReceivedNote::STATUS_APPROVED)>Approved</option>
                <option value="{{ \App\Models\GoodsReceivedNote::STATUS_REJECTED }}" @selected(request('status') === \App\Models\GoodsReceivedNote::STATUS_REJECTED)>Rejected</option>
            </select>

            <select name="supplier_id" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-secondary focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                <option value="">All Suppliers</option>
                @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected(request('supplier_id') === $supplier->id)>{{ $supplier->name }}</option>
                @endforeach
            </select>

            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-secondary focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-secondary focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">

            <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">Filter</button>
            <a href="{{ route('manager.procurement.grn-approvals') }}" class="inline-flex w-full items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50">Reset</a>
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-lg">
            <div class="text-sm text-gray-500">Pending Action</div>
            <div class="mt-2 text-3xl font-extrabold text-amber-600">{{ $grns->getCollection()->whereIn('status', [\App\Models\GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER, \App\Models\GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL])->count() }}</div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-lg">
            <div class="text-sm text-gray-500">Approved</div>
            <div class="mt-2 text-3xl font-extrabold text-green-600">{{ $grns->getCollection()->where('status', \App\Models\GoodsReceivedNote::STATUS_APPROVED)->count() }}</div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-lg">
            <div class="text-sm text-gray-500">Rejected/Returned</div>
            <div class="mt-2 text-3xl font-extrabold text-red-600">{{ $grns->getCollection()->where('status', \App\Models\GoodsReceivedNote::STATUS_REJECTED)->count() }}</div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-lg">
            <div class="text-sm text-gray-500">Total Value</div>
            <div class="mt-2 text-2xl font-extrabold text-secondary"><x-money :amount="$grns->getCollection()->sum('grand_total')" /></div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-indigo-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">GRN</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Supplier</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Received By</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Receipt</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($grns as $grn)
                <tr class="hover:bg-indigo-50/40 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-secondary">{{ $grn->grn_number }}</div>
                        <div class="text-xs text-gray-500">{{ $grn->received_date->format('M d, Y') }}</div>
                        <div class="text-xs text-gray-500">LPO: {{ $grn->lpo->lpo_number ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-secondary">{{ $grn->supplierName }}</div>
                        <div class="text-xs text-gray-500"><x-money :amount="$grn->grand_total" /></div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-secondary">{{ $grn->receiver->name ?? 'N/A' }}</div>
                        @if($grn->confirmer)
                        <div class="text-xs text-gray-500">Confirmed by {{ $grn->confirmer->name }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($grn->receipt_path)
                        <a href="{{ Storage::url($grn->receipt_path) }}" target="_blank" class="inline-flex items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">View Receipt</a>
                        @else
                        <span class="inline-flex items-center justify-center rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">Not uploaded</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">@include('components.grn-status-badge', ['status' => $grn->status])</td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('procurement.grn.show', $grn) }}" class="inline-flex items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">Review</a>

                            @if(in_array($grn->status, [\App\Models\GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER, \App\Models\GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL], true))
                            <form method="POST" action="{{ route('procurement.grn.approve', $grn) }}" onsubmit="return confirm('Approve this GRN and post stock/accounting updates?');">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 transition hover:bg-green-100">Approve</button>
                            </form>
                            <button type="button" onclick="document.getElementById('reject-modal-{{ $grn->id }}').classList.remove('hidden')" class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100">Return</button>
                            @endif
                        </div>
                    </td>
                </tr>

                @if(in_array($grn->status, [\App\Models\GoodsReceivedNote::STATUS_CONFIRMED_BY_STOREKEEPER, \App\Models\GoodsReceivedNote::STATUS_PENDING_MANAGER_APPROVAL], true))
                <tr id="reject-modal-{{ $grn->id }}" class="hidden">
                    <td colspan="6" class="bg-red-50 px-6 py-4">
                        <form method="POST" action="{{ route('procurement.grn.reject', $grn) }}" class="flex flex-col gap-3 md:flex-row md:items-end">
                            @csrf
                            <div class="flex-1">
                                <label class="mb-2 block text-sm font-medium text-gray-700">Return reason</label>
                                <textarea name="rejection_reason" rows="2" class="w-full rounded-lg border border-gray-300 px-4 py-2.5" required></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" onclick="document.getElementById('reject-modal-{{ $grn->id }}').classList.add('hidden')" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700">Cancel</button>
                                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-700">Return GRN</button>
                            </div>
                        </form>
                    </td>
                </tr>
                @endif
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-sm text-gray-500">No GRNs matched your filters.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($grns->hasPages())
    <div>{{ $grns->links() }}</div>
    @endif
</div>
@endsection
