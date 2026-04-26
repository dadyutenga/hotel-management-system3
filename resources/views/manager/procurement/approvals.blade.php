@extends('layouts.app')

@section('title', 'LPO Approval Dashboard')
@section('page-title', 'LPO Approval Dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">LPO Approval Dashboard</h2>
            <p class="mt-1 text-sm text-gray-500">Review submitted purchase orders and enforce approval controls</p>
        </div>
        <form method="GET" class="grid w-full grid-cols-1 gap-3 sm:grid-cols-2 xl:max-w-4xl xl:grid-cols-5">
            <select name="status" class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-secondary focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                <option value="">All Statuses</option>
                @foreach(['draft', 'pending_approval', 'approved', 'rejected', 'sent', 'partially_received', 'fully_received'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                @endforeach
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
        </form>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-lg">
            <div class="text-sm text-gray-500">Pending Approval</div>
            <div class="mt-2 text-3xl font-extrabold text-amber-600">{{ $lpos->getCollection()->where('status', 'pending_approval')->count() }}</div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-lg">
            <div class="text-sm text-gray-500">Approved</div>
            <div class="mt-2 text-3xl font-extrabold text-green-600">{{ $lpos->getCollection()->where('status', 'approved')->count() }}</div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-lg">
            <div class="text-sm text-gray-500">Rejected</div>
            <div class="mt-2 text-3xl font-extrabold text-red-600">{{ $lpos->getCollection()->where('status', 'rejected')->count() }}</div>
        </div>
        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-lg">
            <div class="text-sm text-gray-500">Total Value</div>
            <div class="mt-2 text-2xl font-extrabold text-secondary"><x-money :amount="$lpos->getCollection()->sum('grand_total')" /></div>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-lg">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-indigo-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">LPO</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Supplier</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Submitted By</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Receiving</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-indigo-600">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($lpos as $lpo)
                @php
                    $ordered = (float) $lpo->items->sum('quantity');
                    $received = (float) $lpo->items->sum('received_quantity');
                @endphp
                <tr class="hover:bg-indigo-50/40 transition-colors">
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-secondary">{{ $lpo->lpo_number }}</div>
                        <div class="text-xs text-gray-500">{{ $lpo->order_date->format('M d, Y') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-secondary">{{ $lpo->supplierName }}</div>
                        <div class="text-xs text-gray-500"><x-money :amount="$lpo->grand_total" /></div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-secondary">{{ $lpo->creator->name }}</div>
                        <div class="text-xs text-gray-500">
                            @if($lpo->status === 'rejected' && $lpo->rejector)
                                Rejected by {{ $lpo->rejector->name }}
                            @elseif($lpo->approver)
                                Approved by {{ $lpo->approver->name }}
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-semibold text-secondary">{{ number_format($received, 2) }} / {{ number_format($ordered, 2) }}</div>
                        <div class="text-xs text-gray-500">{{ $ordered > 0 ? number_format(($received / $ordered) * 100, 0) : 0 }}% received</div>
                    </td>
                    <td class="px-6 py-4">@include('components.lpo-status-badge', ['status' => $lpo->status])</td>
                    <td class="px-6 py-4 text-sm">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('procurement.lpo.show', $lpo) }}" class="inline-flex items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-100">View</a>
                            @if($lpo->status === 'pending_approval')
                            <form method="POST" action="{{ route('procurement.lpo.approve', $lpo) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center justify-center rounded-lg border border-green-200 bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700 transition hover:bg-green-100">Approve</button>
                            </form>
                            <button type="button" onclick="document.getElementById('reject-modal-{{ $lpo->id }}').classList.remove('hidden')" class="inline-flex items-center justify-center rounded-lg border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 transition hover:bg-red-100">Reject</button>
                            @endif
                        </div>
                    </td>
                </tr>

                @if($lpo->status === 'pending_approval')
                <tr id="reject-modal-{{ $lpo->id }}" class="hidden">
                    <td colspan="6" class="bg-red-50 px-6 py-4">
                        <form method="POST" action="{{ route('procurement.lpo.reject', $lpo) }}" class="flex flex-col gap-3 md:flex-row md:items-end">
                            @csrf
                            <div class="flex-1">
                                <label class="mb-2 block text-sm font-medium text-gray-700">Rejection reason</label>
                                <textarea name="rejection_reason" rows="2" class="w-full rounded-lg border border-gray-300 px-4 py-2.5" required></textarea>
                            </div>
                            <div class="flex gap-3">
                                <button type="button" onclick="document.getElementById('reject-modal-{{ $lpo->id }}').classList.add('hidden')" class="rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700">Cancel</button>
                                <button type="submit" class="rounded-lg bg-red-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-700">Reject</button>
                            </div>
                        </form>
                    </td>
                </tr>
                @endif
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center text-sm text-gray-500">No purchase orders matched your filters.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($lpos->hasPages())
    <div>{{ $lpos->links() }}</div>
    @endif
</div>
@endsection
