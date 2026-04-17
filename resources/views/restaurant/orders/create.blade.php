@extends('restaurant.layout')

@section('title', __('general.nav.new_order'))

@section('content')
<div class="max-w-5xl mx-auto" x-data="orderForm(@js($categories))">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ __('general.nav.new_order') }}</h1>

    <form method="POST" action="{{ route('restaurant.orders.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-lg shadow p-5 space-y-4">
            <h2 class="font-semibold text-gray-700 text-lg">{{ __('general.details') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.fields.section') }} *</label>
                    <select name="location_id" x-model="locationId" @change="onLocationChanged" required class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">{{ __('general.restaurant.placeholders.select_section') }}</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.nav.tables') }}</label>
                    <select name="table_id" class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">{{ __('general.restaurant.placeholders.no_table_takeaway') }}</option>
                        @foreach($tables as $table)
                            <option value="{{ $table->id }}" {{ $table->status !== 'available' ? 'disabled' : '' }}>
                                {{ __('general.nav.tables') }} {{ $table->table_number }} ({{ $table->capacity }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.fields.order_type') }} *</label>
                    <select name="order_type" x-model="orderType" required class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="walkin">{{ __('general.restaurant.order_type.walkin') }}</option>
                        <option value="guest">{{ __('general.restaurant.order_type.guest') }}</option>
                    </select>
                </div>
                <div x-show="orderType === 'guest'">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.fields.booking_id') }} *</label>
                    <input type="text" name="booking_id" class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                </div>
                <div x-show="orderType === 'walkin'">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.restaurant.fields.customer_name') }} *</label>
                    <input type="text" name="customer_name" class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('general.notes') }}</label>
                <textarea name="notes" rows="2" class="w-full border-gray-300 rounded px-3 py-2 text-sm"></textarea>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-700 text-lg">{{ __('general.restaurant.menu.items') }}</h2>
                <button type="button" @click="addItem()" class="text-xs bg-gray-100 text-gray-700 px-3 py-1 rounded">+ {{ __('general.add') }}</button>
            </div>

            <template x-for="(item, idx) in items" :key="idx">
                <div class="border rounded p-3 mb-3 bg-gray-50 space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-2">
                        <div class="md:col-span-3">
                            <select :name="'items['+idx+'][menu_item_id]'" x-model="item.menu_item_id" @change="onMenuItemChanged(idx)" required class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                                <option value="">{{ __('general.restaurant.placeholders.select_item') }}</option>
                                <template x-for="category in filteredCategories" :key="category.id">
                                    <optgroup :label="category.name">
                                        <template x-for="menu in category.menu_items" :key="menu.id">
                                            <option :value="menu.id" :disabled="!menu.is_available" x-text="menu.name + ' — ' + money(menu.selling_price) + (menu.is_available ? '' : ' ({{ __('general.restaurant.status.unavailable') }})')"></option>
                                        </template>
                                    </optgroup>
                                </template>
                            </select>
                        </div>
                        <div>
                            <input type="number" min="1" required :name="'items['+idx+'][quantity]'" x-model="item.quantity" class="w-full border-gray-300 rounded px-3 py-2 text-sm" :placeholder="'{{ __('general.quantity') }}'">
                        </div>
                        <div>
                            <button type="button" @click="removeItem(idx)" class="w-full border border-red-300 text-red-700 rounded px-2 py-2 text-sm">✕</button>
                        </div>
                    </div>

                    <div x-show="item.option_groups.length > 0" class="space-y-2">
                        <template x-for="group in item.option_groups" :key="group.id">
                            <div class="bg-white border rounded p-2">
                                <div class="text-sm font-medium">
                                    <span x-text="group.name"></span>
                                    <span class="text-xs text-red-600" x-show="group.is_required">*</span>
                                </div>
                                <div class="text-xs text-gray-500 mb-1" x-text="group.selection_type === 'single' ? '{{ __('general.restaurant.options.single') }}' : '{{ __('general.restaurant.options.multiple') }}'"></div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-1">
                                    <template x-for="value in group.values" :key="value.id">
                                        <label class="inline-flex items-center gap-2 text-sm">
                                            <input
                                                :type="group.selection_type === 'single' ? 'radio' : 'checkbox'"
                                                :name="'group_ui_'+idx+'_'+group.id"
                                                :value="value.id"
                                                :checked="item.selected_option_value_ids.includes(value.id)"
                                                @change="toggleOption(idx, group, value.id, $event.target.checked)"
                                            >
                                            <span x-text="value.label + ' (+' + money(value.price_delta) + ')'"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <template x-for="(optionId, optionIndex) in item.selected_option_value_ids" :key="optionId + '_' + optionIndex">
                            <input type="hidden" :name="'items['+idx+'][selected_option_value_ids]['+optionIndex+']'" :value="optionId">
                        </template>
                    </div>

                    <input type="text" :name="'items['+idx+'][notes]'" x-model="item.notes" class="w-full border-gray-300 rounded px-3 py-2 text-sm" :placeholder="'{{ __('general.notes') }}'">
                </div>
            </template>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-primary text-white px-6 py-2 rounded text-sm">{{ __('general.restaurant.orders.create_order') }}</button>
            <a href="{{ route('restaurant.orders.index') }}" class="px-6 py-2 text-sm text-gray-600">{{ __('general.cancel') }}</a>
        </div>
    </form>
</div>

<script>
function orderForm(categories) {
    return {
        locationId: '{{ request('location_id') }}',
        orderType: 'walkin',
        allCategories: categories,
        items: [{ menu_item_id: '', quantity: 1, notes: '', option_groups: [], selected_option_value_ids: [] }],
        money(amount) { return Number(amount || 0).toLocaleString(); },
        get filteredCategories() {
            if (!this.locationId) return this.allCategories;
            return this.allCategories.filter(c => c.location_id === this.locationId);
        },
        addItem() {
            this.items.push({ menu_item_id: '', quantity: 1, notes: '', option_groups: [], selected_option_value_ids: [] });
        },
        removeItem(idx) { this.items.splice(idx, 1); },
        onLocationChanged() {
            this.items = this.items.map(item => ({
                ...item,
                menu_item_id: '',
                option_groups: [],
                selected_option_value_ids: [],
            }));
        },
        onMenuItemChanged(idx) {
            const item = this.items[idx];
            item.selected_option_value_ids = [];
            const menu = this.findMenuItem(item.menu_item_id);
            item.option_groups = menu?.option_groups ?? [];
        },
        findMenuItem(menuItemId) {
            for (const category of this.filteredCategories) {
                const found = (category.menu_items || []).find(m => m.id === menuItemId);
                if (found) return found;
            }
            return null;
        },
        toggleOption(idx, group, valueId, checked) {
            const item = this.items[idx];
            const existing = [...item.selected_option_value_ids];
            const groupValueIds = (group.values || []).map(v => v.id);

            let next = existing.filter(id => !groupValueIds.includes(id));
            const selectedInGroup = existing.filter(id => groupValueIds.includes(id));

            if (group.selection_type === 'single') {
                if (checked) next.push(valueId);
            } else {
                next = next.concat(selectedInGroup.filter(id => id !== valueId));
                if (checked) next.push(valueId);
            }

            item.selected_option_value_ids = [...new Set(next)];
        }
    };
}
</script>
@endsection

