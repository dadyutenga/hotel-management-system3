@extends('layouts.app')

@section('title', 'Request Drinks for Room')
@section('page-title', 'Request Drinks for Room')

@section('content')
<div x-data="drinkRequest()" class="max-w-3xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Request Drinks for Room</h1>

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('reception.drinks.store') }}" @submit="validateBeforeSubmit(event)">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Guest / Room *</label>
                <select name="booking_id" x-model="bookingId" required
                        class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none @error('booking_id') border-red-400 @enderror">
                    <option value="">Select a checked-in guest...</option>
                    @foreach($checkedInGuests as $booking)
                    <option value="{{ $booking->id }}" data-room="{{ $booking->room->room_number ?? '—' }}">
                        {{ $booking->guest_name }} — Room {{ $booking->room->room_number ?? '—' }}
                    </option>
                    @endforeach
                </select>
                @error('booking_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-gray-700">Drink Items *</label>
                    <button type="button" @click="showProductPicker = true"
                            class="text-xs text-primary hover:text-blue-700 font-medium">+ Add Item</button>
                </div>

                <template x-for="(item, idx) in items" :key="idx">
                    <div class="flex items-center gap-2 mb-2 p-2 rounded-lg bg-gray-50">
                        <div class="flex-1">
                            <span class="text-sm font-medium text-gray-700" x-text="item.name"></span>
                            <input type="hidden" :name="'items['+idx+'][product_name]'" x-model="item.name">
                        </div>
                        <input type="number" :name="'items['+idx+'][quantity]'" x-model.number="item.qty"
                               min="1" max="99" class="w-16 text-center border border-gray-200 rounded-lg text-sm py-1">
                        <button type="button" @click="items.splice(idx, 1)"
                                class="text-red-400 hover:text-red-600 text-sm px-1">✕</button>
                    </div>
                </template>

                <div x-show="items.length === 0" class="text-sm text-gray-400 py-2">No items added yet.</div>
                @error('items')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                <textarea name="notes" rows="2" placeholder="Any special instructions..."
                          class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" :disabled="items.length === 0 || !bookingId"
                        class="bg-primary text-white px-5 py-2 rounded-xl hover:bg-blue-700 text-sm font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                    Send Drink Request to Bar
                </button>
                <a href="{{ route('dashboard') }}" class="bg-gray-200 text-gray-700 px-5 py-2 rounded-xl hover:bg-gray-300 text-sm font-medium">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    {{-- Product Picker Modal --}}
    <div x-show="showProductPicker" class="fixed inset-0 z-50 flex items-center justify-center" x-cloak>
        <div class="absolute inset-0 bg-black bg-opacity-40" @click="showProductPicker = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[80vh] flex flex-col">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">Select Drink</h3>
                <button type="button" @click="showProductPicker = false" class="text-gray-400 hover:text-gray-600 text-xl">&times;</button>
            </div>
            <div class="p-4 border-b border-gray-100">
                <input type="text" x-model="productSearch" placeholder="Search drinks..."
                       class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-1">
                <template x-for="product in filteredProducts" :key="product.name">
                    <div @click="addItem(product); showProductPicker = false"
                         class="cursor-pointer p-3 rounded-lg border border-gray-100 hover:border-blue-300 hover:bg-blue-50/50 transition-all flex justify-between items-center">
                        <div>
                            <div class="text-sm font-semibold text-gray-800" x-text="product.name"></div>
                            <div class="text-xs text-gray-400" x-text="(product.stock > 0 ? product.stock + ' available' : 'Out of Stock')"></div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-primary" x-text="formatCurrency(product.price)"></span>
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                    </div>
                </template>
                <div x-show="filteredProducts.length === 0" class="text-center py-4 text-gray-400 text-sm">
                    No matching drinks found.
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function drinkRequest() {
    return {
        bookingId: '{{ old('booking_id', '') }}',
        items: [],
        showProductPicker: false,
        productSearch: '',

        allProducts: {!! json_encode($barProducts->map(fn($p) => [
            'name'  => $p->name,
            'price' => (float) $p->selling_price,
            'stock' => $stockMap[$p->name] ?? 0,
        ])) !!},

        get filteredProducts() {
            const q = this.productSearch.toLowerCase();
            return this.allProducts.filter(p => p.name.toLowerCase().includes(q));
        },

        addItem(product) {
            const existing = this.items.find(i => i.name === product.name);
            if (existing) {
                existing.qty++;
            } else {
                this.items.push({ name: product.name, qty: 1 });
            }
            this.productSearch = '';
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('en-TZ', { minimumFractionDigits: 0 }).format(value) + ' TZS';
        },

        validateBeforeSubmit(e) {
            if (this.items.length === 0) {
                e.preventDefault();
                alert('Please add at least one drink item.');
            }
            if (!this.bookingId) {
                e.preventDefault();
                alert('Please select a guest/room.');
            }
        },
    }
}
</script>
@endpush
@endsection
