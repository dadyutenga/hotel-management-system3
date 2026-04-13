{{-- resources/views/procurement/suppliers/show.blade.php --}}
@extends('layouts.app')

@section('title', $supplier->name)
@section('page-title', 'Supplier Details')

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $supplier->name }}</h2>
            <p class="text-sm text-gray-500 mt-1">Supplier profile and recent procurement activity</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('procurement.suppliers.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Back</a>
            <a href="{{ route('procurement.suppliers.edit', $supplier) }}" class="px-4 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-primary to-blue-600 rounded-lg hover:shadow-lg transition-all">Edit Supplier</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100 p-6 space-y-6">
            <div>
                <h3 class="text-lg font-bold text-secondary mb-4">Supplier Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Contact Person</p>
                        <p class="mt-1 font-semibold text-secondary">{{ $supplier->contact_person ?: 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Phone</p>
                        <p class="mt-1 font-semibold text-secondary">{{ $supplier->phone ?: 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Email</p>
                        <p class="mt-1 font-semibold text-secondary">{{ $supplier->email ?: 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">Status</p>
                        <p class="mt-1 font-semibold {{ $supplier->is_active ? 'text-green-600' : 'text-gray-500' }}">{{ $supplier->is_active ? 'Active' : 'Inactive' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">TIN Number</p>
                        <p class="mt-1 font-semibold text-secondary">{{ $supplier->tin_number ?: 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500">VRN Number</p>
                        <p class="mt-1 font-semibold text-secondary">{{ $supplier->vrn_number ?: 'Not provided' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-500">Address</p>
                        <p class="mt-1 font-semibold text-secondary">{{ $supplier->address ?: 'Not provided' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-gray-500">Notes</p>
                        <p class="mt-1 font-semibold text-secondary">{{ $supplier->notes ?: 'No notes added' }}</p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-bold text-secondary mb-4">Recent Purchase Orders</h3>
                <div class="space-y-3">
                    @forelse($supplier->purchaseOrders as $lpo)
                    <div class="flex items-center justify-between rounded-xl border border-gray-100 p-4">
                        <div>
                            <p class="font-semibold text-secondary">{{ $lpo->lpo_number }}</p>
                            <p class="text-xs text-gray-500">{{ $lpo->order_date?->format('M d, Y') }}</p>
                        </div>
                        <a href="{{ route('procurement.lpo.show', $lpo) }}" class="text-sm font-semibold text-primary hover:text-blue-700">View</a>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No purchase orders recorded for this supplier yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Activity Summary</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Purchase Orders</p>
                        <p class="mt-1 text-3xl font-extrabold text-secondary">{{ $supplier->purchaseOrders->count() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Goods Received Notes</p>
                        <p class="mt-1 text-3xl font-extrabold text-secondary">{{ $supplier->goodsReceivedNotes->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-secondary mb-4">Recent GRNs</h3>
                <div class="space-y-3">
                    @forelse($supplier->goodsReceivedNotes as $grn)
                    <div class="rounded-xl border border-gray-100 p-4">
                        <p class="font-semibold text-secondary">{{ $grn->grn_number }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $grn->received_date?->format('M d, Y') ?: 'Pending date' }}</p>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No goods received notes yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
