@extends('store.layout')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('store.beverages.show', $beverage) }}" class="text-sm text-primary hover:text-blue-700 font-medium">&larr; Back</a>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Edit Beverage</h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <form method="POST" action="{{ route('store.beverages.update', $beverage) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Barcode</label>
                <input type="text" name="barcode" value="{{ old('barcode', $beverage->barcode) }}" required
                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary">
                @error('barcode') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $beverage->name) }}" required
                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Category</label>
                    <select name="category_id" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
                        <option value="">Select category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $beverage->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Unit</label>
                    <select name="unit" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
                        @foreach(['bottle','can','crate','pack'] as $u)
                            <option value="{{ $u }}" {{ old('unit', $beverage->unit) == $u ? 'selected' : '' }}>{{ ucfirst($u) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Buying Price</label>
                    <input type="number" name="buying_price" value="{{ old('buying_price', $beverage->buying_price) }}" step="0.01" min="0" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Selling Price</label>
                    <input type="number" name="selling_price" value="{{ old('selling_price', $beverage->selling_price) }}" step="0.01" min="0" required
                        class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Reorder Level</label>
                <input type="number" name="reorder_level" value="{{ old('reorder_level', $beverage->reorder_level) }}" min="0"
                    class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg text-sm">{{ old('description', $beverage->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Product Image</label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/jpg,image/webp"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-sm file:bg-primary/10 file:text-primary">
            </div>

            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-primary hover:bg-blue-700 text-white rounded-xl text-sm font-semibold">Update Beverage</button>
                <a href="{{ route('store.beverages.show', $beverage) }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
