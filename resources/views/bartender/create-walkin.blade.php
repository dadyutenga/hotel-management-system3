@extends('layouts.app')

@section('title', __('bartender.titles.walkin_create'))
@section('page-title', __('bartender.titles.walkin_create'))

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl border border-gray-100 shadow-sm p-5" x-data="walkinDrinkForm()">
    <form method="POST" action="{{ route('bartender.orders.walkin.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-gray-600 mb-1">{{ __('bartender.fields.customer_name_optional') }}</label>
            <input type="text" name="customer_name" class="w-full border-gray-300 rounded-lg text-sm px-3 py-2" placeholder="{{ __('bartender.placeholders.walkin_guest') }}">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">{{ __('bartender.fields.notes') }}</label>
            <textarea name="notes" rows="2" class="w-full border-gray-300 rounded-lg text-sm px-3 py-2"></textarea>
        </div>

        <h2 class="font-semibold text-gray-800">{{ __('bartender.fields.drink_items') }}</h2>
        <template x-for="(item, idx) in items" :key="idx">
            <div class="flex gap-2">
                <select :name="`items[${idx}][menu_item_id]`" x-model="item.menu_item_id" required class="flex-1 border-gray-300 rounded-lg text-sm px-3 py-2">
                    <option value="">{{ __('bartender.fields.select_drink') }}</option>
                    @foreach($categories as $cat)
                        @if($cat->menuItems->count())
                            <optgroup label="{{ $cat->name }}">
                                @foreach($cat->menuItems as $menuItem)
                                    <option value="{{ $menuItem->id }}">{{ $menuItem->name }} - {{ number_format($menuItem->selling_price, 0) }} TZS</option>
                                @endforeach
                            </optgroup>
                        @endif
                    @endforeach
                </select>
                <input type="number" :name="`items[${idx}][quantity]`" x-model="item.quantity" min="1" class="w-24 border-gray-300 rounded-lg text-sm px-3 py-2" required>
                <input type="text" :name="`items[${idx}][notes]`" x-model="item.notes" placeholder="{{ __('bartender.fields.note') }}" class="w-40 border-gray-300 rounded-lg text-sm px-3 py-2">
                <button type="button" @click="removeItem(idx)" class="px-3 py-2 rounded-lg border border-gray-300 text-gray-600">{{ __('bartender.actions.remove_item') }}</button>
            </div>
        </template>

        <button type="button" @click="addItem()" class="px-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-700">{{ __('bartender.actions.add_item') }}</button>

        <div class="pt-3">
            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">{{ __('bartender.actions.create_walkin_order') }}</button>
        </div>
    </form>
</div>

<script>
function walkinDrinkForm() {
    return {
        items: [{ menu_item_id: '', quantity: 1, notes: '' }],
        addItem() {
            this.items.push({ menu_item_id: '', quantity: 1, notes: '' });
        },
        removeItem(idx) {
            if (this.items.length === 1) {
                return;
            }
            this.items.splice(idx, 1);
        },
    }
}
</script>
@endsection
