@extends('layouts.app')

@section('title', 'New Room Service Drink Order')
@section('page-title', 'New Room Service Drink Order')

@section('content')
<div class="max-w-4xl mx-auto bg-white rounded-xl border border-gray-100 shadow-sm p-5" x-data="roomServiceDrinkForm()">
    <form method="POST" action="{{ route('bartender.orders.room-service.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-gray-600 mb-1">Booking *</label>
            <select name="booking_id" required class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
                <option value="">Select Active Booking</option>
                @foreach($bookings as $booking)
                    <option value="{{ $booking->id }}">{{ $booking->booking_number }} - {{ $booking->guest_display_name }} - Room {{ $booking->room->room_number ?? 'N/A' }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm text-gray-600 mb-1">Notes</label>
            <textarea name="notes" rows="2" class="w-full border-gray-300 rounded-lg text-sm px-3 py-2"></textarea>
        </div>

        <h2 class="font-semibold text-gray-800">Drink Items</h2>
        <template x-for="(item, idx) in items" :key="idx">
            <div class="flex gap-2">
                <select :name="`items[${idx}][menu_item_id]`" x-model="item.menu_item_id" required class="flex-1 border-gray-300 rounded-lg text-sm px-3 py-2">
                    <option value="">Select Drink</option>
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
                <input type="text" :name="`items[${idx}][notes]`" x-model="item.notes" placeholder="Note" class="w-40 border-gray-300 rounded-lg text-sm px-3 py-2">
                <button type="button" @click="removeItem(idx)" class="px-3 py-2 rounded-lg border border-gray-300 text-gray-600">Remove</button>
            </div>
        </template>

        <button type="button" @click="addItem()" class="px-3 py-2 rounded-lg border border-gray-300 text-sm text-gray-700">+ Add Item</button>

        <div class="pt-3">
            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Create Room Service Drink Order</button>
        </div>
    </form>
</div>

<script>
function roomServiceDrinkForm() {
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
