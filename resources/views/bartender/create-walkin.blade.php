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
            <label class="block text-sm text-gray-600 mb-1">{{ __('bartender.fields.customer_phone_optional') }}</label>
            <input type="text" name="customer_phone" class="w-full border-gray-300 rounded-lg text-sm px-3 py-2" placeholder="{{ __('bartender.placeholders.customer_phone') }}">
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">{{ __('bartender.fields.notes') }}</label>
            <textarea name="notes" rows="2" class="w-full border-gray-300 rounded-lg text-sm px-3 py-2"></textarea>
        </div>

        <h2 class="font-semibold text-gray-800">{{ __('bartender.fields.drink_items') }}</h2>
        <template x-for="(item, idx) in items" :key="idx">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                <select :name="`items[${idx}][menu_item_id]`" x-model="item.menu_item_id" @change="applyPrice(idx)" required class="md:col-span-5 border-gray-300 rounded-lg text-sm px-3 py-2">
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
                <input type="number" :name="`items[${idx}][quantity]`" x-model.number="item.quantity" min="1" class="md:col-span-1 border-gray-300 rounded-lg text-sm px-3 py-2" required>
                <div class="md:col-span-2 text-sm text-gray-600" x-text="formatCurrency(item.unit_price)"></div>
                <div class="md:col-span-2 text-sm font-semibold text-gray-800" x-text="formatCurrency(lineSubtotal(item))"></div>
                <input type="text" :name="`items[${idx}][notes]`" x-model="item.notes" placeholder="{{ __('bartender.fields.note') }}" class="md:col-span-1 border-gray-300 rounded-lg text-sm px-3 py-2">
                <button type="button" @click="removeItem(idx)" class="md:col-span-1 px-3 py-2 rounded-lg border border-gray-300 text-gray-600">{{ __('bartender.actions.remove_item') }}</button>
            </div>
        </template>

        <button type="button" @click="addItem()" class="px-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-700">{{ __('bartender.actions.add_item') }}</button>
        <div class="text-right text-lg font-bold text-gray-800">{{ __('bartender.fields.total') }}: <span x-text="formatCurrency(orderTotal())"></span></div>

        <div class="pt-3">
            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">{{ __('bartender.actions.create_walkin_order') }}</button>
        </div>
    </form>
</div>

<script>
function walkinDrinkForm() {
    const menuPrices = @json(
        $categories
            ->flatMap(fn($cat) => $cat->menuItems->map(fn($item) => ['id' => $item->id, 'price' => (float) $item->selling_price]))
            ->pluck('price', 'id')
    );

    return {
        items: [{ menu_item_id: '', quantity: 1, notes: '', unit_price: 0 }],
        addItem() {
            this.items.push({ menu_item_id: '', quantity: 1, notes: '', unit_price: 0 });
        },
        removeItem(idx) {
            if (this.items.length === 1) {
                return;
            }
            this.items.splice(idx, 1);
        },
        applyPrice(idx) {
            const selected = this.items[idx].menu_item_id;
            this.items[idx].unit_price = Number(menuPrices[selected] ?? 0);
        },
        lineSubtotal(item) {
            return Number(item.unit_price || 0) * Number(item.quantity || 0);
        },
        orderTotal() {
            return this.items.reduce((sum, item) => sum + this.lineSubtotal(item), 0);
        },
        formatCurrency(value) {
            return new Intl.NumberFormat('en-TZ', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0,
            }).format(Number(value || 0)) + ' TZS';
        },
    }
}
</script>
@endsection
