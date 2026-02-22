{{-- resources/views/restaurant/orders/create.blade.php --}}
@extends('restaurant.layout')

@section('title', 'New Order')

@section('content')
<div class="max-w-4xl mx-auto" x-data="orderForm()">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">New Order</h1>

    <form method="POST" action="{{ route('restaurant.orders.store') }}" class="space-y-6">
        @csrf

        {{-- Order setup --}}
        <div class="bg-white rounded-lg shadow p-5 space-y-4">
            <h2 class="font-semibold text-gray-700 text-lg">Order Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Section/Location --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Section *</label>
                    <select name="location_id" x-model="location_id" required
                            class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">— Select section —</option>
                        @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                        @endforeach
                    </select>
                    @error('location_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Table --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Table</label>
                    <select name="table_id" class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">— No table (takeaway) —</option>
                        @foreach($tables as $table)
                        <option value="{{ $table->id }}" {{ $table->status !== 'available' ? 'disabled' : '' }}>
                            Table {{ $table->table_number }} ({{ $table->capacity }} seats)
                            {{ $table->status !== 'available' ? " — {$table->status}" : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Order type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Type *</label>
                    <select name="order_type" x-model="order_type" required
                            class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="walkin">Walk-in Customer</option>
                        <option value="guest">Hotel Guest</option>
                    </select>
                </div>

                {{-- Booking ID (for guest orders) --}}
                <div x-show="order_type === 'guest'">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Booking ID *</label>
                    <input type="text" name="booking_id" placeholder="Paste booking UUID"
                           class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                    @error('booking_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Customer name (for walk-in) --}}
                <div x-show="order_type === 'walkin'">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name *</label>
                    <input type="text" name="customer_name" placeholder="Customer name"
                           class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                    @error('customer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full border-gray-300 rounded px-3 py-2 text-sm"
                          placeholder="Optional order notes"></textarea>
            </div>
        </div>

        {{-- Menu items --}}
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-semibold text-gray-700 text-lg">Order Items</h2>
                <button type="button" @click="addItem()"
                        class="text-xs bg-gray-100 text-gray-600 px-3 py-1 rounded hover:bg-gray-200">
                    + Add Item
                </button>
            </div>

            @error('items') <p class="text-red-500 text-xs mb-3">{{ $message }}</p> @enderror

            <template x-for="(item, idx) in items" :key="idx">
                <div class="flex gap-2 mb-3 items-start p-3 bg-gray-50 rounded">
                    <div class="flex-1">
                        <select :name="'items['+idx+'][menu_item_id]'" x-model="item.menu_item_id" required
                                class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                            <option value="">— Select item —</option>
                            @foreach($categories as $cat)
                                @if($cat->menuItems->count())
                                <optgroup label="{{ $cat->name }} ({{ $cat->location->name }})">
                                    @foreach($cat->menuItems as $mi)
                                    <option value="{{ $mi->id }}">{{ $mi->name }} — {{ number_format($mi->selling_price, 0) }} TZS</option>
                                    @endforeach
                                </optgroup>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <input type="number" :name="'items['+idx+'][quantity]'" x-model="item.quantity"
                           min="1" required placeholder="Qty"
                           class="w-20 border-gray-300 rounded px-3 py-2 text-sm">
                    <input type="text" :name="'items['+idx+'][notes]'" x-model="item.notes"
                           placeholder="Notes" maxlength="255"
                           class="w-40 border-gray-300 rounded px-3 py-2 text-sm">
                    <button type="button" @click="items.splice(idx, 1)"
                            class="text-red-500 hover:text-red-700 text-sm px-2 py-2">✕</button>
                </div>
            </template>

            <div x-show="items.length === 0" class="text-center py-6 text-gray-400 text-sm">
                No items added yet. Click "Add Item" to start.
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-primary text-white px-6 py-2 rounded text-sm hover:opacity-90"
                    :disabled="items.length === 0">
                Create Order
            </button>
            <a href="{{ route('restaurant.orders.index') }}" class="px-6 py-2 text-sm text-gray-600 hover:text-gray-800">Cancel</a>
        </div>
    </form>
</div>

<script>
function orderForm() {
    return {
        location_id: '{{ request("location_id") }}',
        order_type: 'walkin',
        items: [{ menu_item_id: '', quantity: 1, notes: '' }],
        addItem() {
            this.items.push({ menu_item_id: '', quantity: 1, notes: '' });
        }
    }
}
</script>
@endsection
