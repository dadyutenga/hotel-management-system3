{{-- resources/views/restaurant/menu/items/edit.blade.php --}}
@extends('restaurant.layout')

@section('title', __('general.edit') . ': ' . $menuItem->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ __('general.restaurant.menu.edit_item') }}</h1>

    <form method="POST" action="{{ route('restaurant.menu.update', $menuItem) }}" x-data="menuItemForm()" class="space-y-6">
        @csrf @method('PUT')

        {{-- Category (read-only display) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.fields.category') }}</label>
            <p class="text-sm text-gray-600 bg-gray-50 rounded px-3 py-2">
                {{ $menuItem->category->name ?? '—' }}
                ({{ $menuItem->category->location->name ?? '' }})
            </p>
        </div>

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.fields.name') }} *</label>
            <input type="text" name="name" value="{{ old('name', $menuItem->name) }}" required
                   class="w-full border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary">
            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.description') }}</label>
            <textarea name="description" rows="2"
                      class="w-full border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary">{{ old('description', $menuItem->description) }}</textarea>
        </div>

        {{-- Selling price --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.fields.base_price_tzs') }} *</label>
            <input type="number" name="selling_price" value="{{ old('selling_price', $menuItem->selling_price) }}"
                   required min="1" step="1"
                   class="w-full border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary">
            @error('selling_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Availability toggle --}}
        <div class="flex items-center gap-3">
            <label class="text-sm font-medium text-gray-700">{{ __('general.restaurant.fields.available') }}?</label>
            <input type="hidden" name="is_available" value="0">
            <input type="checkbox" name="is_available" value="1"
                   {{ old('is_available', $menuItem->is_available) ? 'checked' : '' }}
                   class="rounded text-primary focus:ring-primary">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.fields.service_location_tag') }}</label>
            <input type="text" name="service_location_tag" value="{{ old('service_location_tag', $menuItem->service_location_tag) }}"
                   class="w-full border-gray-300 rounded px-3 py-2 text-sm focus:ring-primary focus:border-primary"
                   placeholder="{{ __('general.restaurant.placeholders.service_location_tag') }}">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.options.attach_groups') }}</label>
            <select name="option_group_ids[]" multiple class="w-full border-gray-300 rounded px-3 py-2 text-sm h-32">
                @foreach($optionGroups as $group)
                    <option value="{{ $group->id }}" @selected($menuItem->optionGroups->contains('id', $group->id))>
                        {{ $group->name }} ({{ ucfirst($group->selection_type) }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Ingredients --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <label class="block text-sm font-medium text-gray-700">{{ __('general.restaurant.menu.ingredients') }}</label>
                <button type="button" @click="addIngredient()"
                        class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded hover:bg-gray-200">
                    + {{ __('general.add') }}
                </button>
            </div>

            <template x-for="(ing, idx) in ingredients" :key="idx">
                <div class="flex gap-2 mb-2 items-start">
                    <select :name="'ingredients['+idx+'][product_id]'" x-model="ing.product_id" required
                            class="flex-1 border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">{{ __('general.restaurant.placeholders.product') }}</option>
                        @foreach($products as $prod)
                        <option value="{{ $prod->id }}" :selected="ing.product_id === '{{ $prod->id }}'">{{ $prod->name }} ({{ $prod->unit }})</option>
                        @endforeach
                    </select>
                    <input type="number" :name="'ingredients['+idx+'][quantity]'" x-model="ing.quantity"
                           step="0.0001" min="0.0001" required :placeholder="'{{ __('general.quantity') }}'"
                           class="w-24 border-gray-300 rounded px-3 py-2 text-sm">
                    <input type="text" :name="'ingredients['+idx+'][unit]'" x-model="ing.unit"
                           required :placeholder="'{{ __('general.unit') }}'" maxlength="30"
                           class="w-24 border-gray-300 rounded px-3 py-2 text-sm">
                    <button type="button" @click="ingredients.splice(idx, 1)"
                            class="text-red-500 hover:text-red-700 text-sm px-2 py-2">✕</button>
                </div>
            </template>
        </div>

        <div class="flex gap-3 pt-4 border-t">
                <button type="submit" class="bg-primary text-white px-6 py-2 rounded text-sm hover:opacity-90">
                {{ __('general.update') }}
            </button>
            <a href="{{ route('restaurant.menu.index') }}" class="px-6 py-2 text-sm text-gray-600 hover:text-gray-800">{{ __('general.cancel') }}</a>
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
