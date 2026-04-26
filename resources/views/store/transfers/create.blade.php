@extends('layouts.app')

@section('title', 'Create Stock Transfer')
@section('page-title', 'Create Stock Transfer')

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Create Stock Transfer</h1>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('store.transfers.store') }}">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                    <select name="product_id" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">
                        <option value="">Select product...</option>
                        @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') === $product->id ? 'selected' : '' }}>
                            {{ $product->name }} ({{ $product->unit }})
                        </option>
                        @endforeach
                    </select>
                    @error('product_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transfer From *</label>
                    <select name="from_location_id" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('from_location_id') border-red-400 @enderror">
                        <option value="">Select source...</option>
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('from_location_id', optional(\App\Models\StockLocation::mainStore())->id) === $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                    @error('from_location_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transfer To *</label>
                    <select name="to_location_id" required class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('to_location_id') border-red-400 @enderror">
                        <option value="">Select destination...</option>
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}" {{ old('to_location_id') === $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                    @error('to_location_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                    <input type="number" name="quantity" value="{{ old('quantity') }}" step="0.001" min="0.001" required
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('quantity') border-red-400 @enderror">
                    @error('quantity')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                    <textarea name="reason" rows="2"
                              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">{{ old('reason') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 mt-6">
                <button type="submit"
                        class="bg-primary text-white px-5 py-2 rounded-xl hover:bg-blue-700 text-sm font-medium">
                    Submit Transfer
                </button>
                <a href="{{ route('store.transfers.index') }}"
                   class="bg-gray-200 text-gray-700 px-5 py-2 rounded-xl hover:bg-gray-300 text-sm font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
