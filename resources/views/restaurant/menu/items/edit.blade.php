{{-- resources/views/restaurant/menu/items/edit.blade.php --}}
@extends('restaurant.layout')

@section('title', 'Edit: ' . $menuItem->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Menu Item</h1>

    <form method="POST" action="{{ route('restaurant.menu.update', $menuItem) }}" x-data="menuItemForm()" class="space-y-6">
        @csrf @method('PUT')

        {{-- Category (read-only display) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <p class="text-sm text-gray-600 bg-gray-50 rounded px-3 py-2">
                {{ $menuItem->category->name ?? '—' }}
                ({{ $menuItem->category->location->name ?? '' }})
            </p>
        </div>

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
            <input type="text" name="name" value="{{ old('name', $menuItem->name) }}" required
                   class="w-full border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="2"
                      class="w-full border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary">{{ old('description', $menuItem->description) }}</textarea>
        </div>

        {{-- Selling price --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price (TZS) *</label>
            <input type="number" name="selling_price" value="{{ old('selling_price', $menuItem->selling_price) }}"
                   required min="1" step="1"
                   class="w-full border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary">
            @error('selling_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Availability toggle --}}
        <div class="flex items-center gap-3">
            <label class="text-sm font-medium text-gray-700">Available?</label>
            <input type="hidden" name="is_available" value="0">
            <input type="checkbox" name="is_available" value="1"
                   {{ old('is_available', $menuItem->is_available) ? 'checked' : '' }}
                   class="rounded text-primary focus:ring-primary">
        </div>

        {{-- Ingredients --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <label class="block text-sm font-medium text-gray-700">Recipe / Ingredients</label>
                <button type="button" @click="addIngredient()"
                        class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded hover:bg-gray-200">
                    + Add Ingredient
                </button>
            </div>

            <template x-for="(ing, idx) in ingredients" :key="idx">
                <div class="flex gap-2 mb-2 items-start">
                    <select :name="'ingredients['+idx+'][product_id]'" x-model="ing.product_id" required
                            class="flex-1 border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">— Product —</option>
                        @foreach($products as $prod)
                        <option value="{{ $prod->id }}" :selected="ing.product_id === '{{ $prod->id }}'">{{ $prod->name }} ({{ $prod->unit }})</option>
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
                Save Changes
            </button>
            <a href="{{ route('restaurant.menu.index') }}" class="px-6 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</a>
        </div>
    </form>
</div>

<script>
function menuItemForm() {
    return {
        ingredients: @json($menuItem->ingredients->map(fn($i) => [
            'product_id' => $i->product_id,
            'quantity'    => $i->quantity,
            'unit'        => $i->unit,
        ])->values()),
        addIngredient() {
            this.ingredients.push({ product_id: '', quantity: '', unit: '' });
        }
    }
}
</script>
@endsection
