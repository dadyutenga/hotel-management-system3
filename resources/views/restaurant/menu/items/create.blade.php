{{-- resources/views/restaurant/menu/items/create.blade.php --}}
@extends('restaurant.layout')

@section('title', 'New Menu Item')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">New Menu Item</h1>

    <form method="POST" action="{{ route('restaurant.menu.store') }}" x-data="menuItemForm()" class="space-y-6">
        @csrf

        {{-- Category --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
            <select name="category_id" required
                    class="w-full border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary">
                <option value="">— Select category —</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id') === $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }} ({{ $cat->location->name }})
                    </option>
                @endforeach
            </select>
            @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="w-full border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary"
                   placeholder="e.g. Gin & Tonic">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="2"
                      class="w-full border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary"
                      placeholder="Optional description">{{ old('description') }}</textarea>
        </div>

        {{-- Selling price --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price (TZS) *</label>
            <input type="number" name="selling_price" value="{{ old('selling_price') }}" required min="1" step="1"
                   class="w-full border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary"
                   placeholder="15000">
            @error('selling_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Ingredients (optional) --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <label class="block text-sm font-medium text-gray-700">Recipe / Ingredients</label>
                <button type="button" @click="addIngredient()"
                        class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded hover:bg-gray-200">
                    + Add Ingredient
                </button>
            </div>
            <p class="text-xs text-gray-400 mb-3">Optional. Ingredients link this menu item to stock for automatic deduction.</p>

            <template x-for="(ing, idx) in ingredients" :key="idx">
                <div class="flex gap-2 mb-2 items-start">
                    <select :name="'ingredients['+idx+'][product_id]'" x-model="ing.product_id" required
                            class="flex-1 border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">— Product —</option>
                        @foreach($products as $prod)
                        <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->unit }})</option>
                        @endforeach
                    </select>
                    <input type="number" :name="'ingredients['+idx+'][quantity]'" x-model="ing.quantity"
                           step="0.0001" min="0.0001" required placeholder="Qty"
                           class="w-24 border-gray-300 rounded px-3 py-2 text-sm">
                    <input type="text" :name="'ingredients['+idx+'][unit]'" x-model="ing.unit"
                           required placeholder="Unit" maxlength="30"
                           class="w-24 border-gray-300 rounded px-3 py-2 text-sm">
                    <button type="button" @click="ingredients.splice(idx, 1)"
                            class="text-red-500 hover:text-red-700 text-sm px-2 py-2">✕</button>
                </div>
            </template>
        </div>

        <div class="flex gap-3 pt-4 border-t">
            <button type="submit" class="bg-primary text-white px-6 py-2 rounded text-sm hover:opacity-90">
                Create Menu Item
            </button>
            <a href="{{ route('restaurant.menu.index') }}" class="px-6 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</a>
        </div>
    </form>
</div>

<script>
function menuItemForm() {
    return {
        ingredients: [],
        addIngredient() {
            this.ingredients.push({ product_id: '', quantity: '', unit: '' });
        }
    }
}
</script>
@endsection
