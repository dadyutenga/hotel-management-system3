@extends('layouts.app')

@section('title', 'Edit Kitchen Stock Item')
@section('page-title', 'Edit Stock Item')

@section('content')
<div class="max-w-lg mx-auto">
    <form method="POST" action="{{ route('manager.kitchen-stock.update', $item) }}" class="bg-white rounded-xl border border-gray-200 p-6">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name', $item->name) }}" required class="w-full border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unit *</label>
                <input type="text" name="unit" value="{{ old('unit', $item->unit) }}" placeholder="e.g. bags, kg, liters, pieces" required class="w-full border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Current Quantity</label>
                <input type="number" name="current_quantity" value="{{ old('current_quantity', $item->current_quantity) }}" step="0.01" min="0" class="w-full border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Quantity (low stock alert)</label>
                <input type="number" name="minimum_quantity" value="{{ old('minimum_quantity', $item->minimum_quantity) }}" step="0.01" min="0" class="w-full border-gray-300 rounded-lg px-3 py-2">
            </div>
            <div class="flex items-center justify-between pt-4">
                <a href="{{ route('manager.kitchen-stock.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">Update Item</button>
            </div>
        </div>
    </form>
</div>
@endsection
