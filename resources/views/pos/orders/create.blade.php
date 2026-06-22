@extends('pos.layout')

@section('page-title', 'New Order')

@section('header-actions')
<a href="{{ route('pos.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition-colors">
    Dashboard
</a>
@endsection

@section('content')
<div x-data="waiterPos()" class="h-[calc(100vh-8rem)] flex gap-4">
    <!-- LEFT: Menu -->
    <div class="flex-1 flex flex-col gap-4 min-w-0">
        <!-- Customer / Order Type -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <input type="text" x-model="customerName" placeholder="Guest / customer name"
                    class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
                <input type="text" x-model="customerPhone" placeholder="Phone (optional)"
                    class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
                <select x-model="orderType" @change="bookingId = ''; selectedBooking = null"
                    class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
                    <option value="walkin">Walk-in</option>
                    <option value="guest">Guest (charge to room)</option>
                </select>
            </div>

            <div x-show="orderType === 'guest'" class="mt-3 relative">
                <input type="text" x-model="bookingSearch" @input.debounce.300ms="searchBookings()"
                    placeholder="Search checked-in guest..."
                    class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
                <div x-show="showBookingDropdown && filteredBookings.length" x-cloak
                    class="absolute z-20 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                    <template x-for="booking in filteredBookings" :key="booking.id">
                        <button type="button" @click="selectBooking(booking)"
                            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 border-b border-gray-100 last:border-0">
                            <span x-text="booking.guest_name"></span>
                            <span class="text-xs text-gray-500 ml-2" x-text="booking.booking_number"></span>
                        </button>
                    </template>
                </div>
                <div x-show="selectedBooking" class="mt-2 text-sm text-primary font-medium">
                    Charging to: <span x-text="selectedBooking?.guest_name"></span>
                    (<span x-text="selectedBooking?.booking_number"></span>)
                    <button type="button" @click="clearBooking()" class="ml-2 text-xs text-red-600 underline">Clear</button>
                </div>
            </div>
        </div>

        <!-- Categories & Items -->
        <div class="flex flex-1 gap-3 min-h-0">
            <div class="w-44 bg-white rounded-xl border border-gray-100 shadow-sm p-2 overflow-y-auto flex-shrink-0">
                <button @click="activeCategory = null"
                    :class="!activeCategory ? 'bg-blue-50 text-primary' : 'text-gray-600 hover:bg-gray-50'"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                    All Items
                </button>
                @foreach($categories as $cat)
                <button @click="activeCategory = '{{ $cat->id }}'"
                    :class="activeCategory === '{{ $cat->id }}' ? 'bg-blue-50 text-primary' : 'text-gray-600 hover:bg-gray-50'"
                    class="w-full text-left px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                    {{ $cat->name }}
                </button>
                @endforeach
            </div>

            <div class="flex-1 bg-white rounded-xl border border-gray-100 shadow-sm p-3 overflow-y-auto">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($categories as $cat)
                        @foreach($cat->menuItems as $item)
                        <button type="button"
                            x-show="!activeCategory || activeCategory === '{{ $cat->id }}'"
                            @click="addItem({{ json_encode(['id' => $item->id, 'name' => $item->name, 'price' => (float) $item->selling_price]) }})"
                            class="flex flex-col items-start p-3 rounded-xl border border-gray-200 hover:border-primary hover:shadow-md transition-all text-left bg-white h-28">
                            <span class="text-sm font-semibold text-gray-800 line-clamp-2">{{ $item->name }}</span>
                            <span class="mt-auto text-sm font-bold text-primary">@currency($item->selling_price, 'TZS')</span>
                        </button>
                        @endforeach
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Cart -->
    <div class="w-96 bg-white rounded-xl border border-gray-100 shadow-sm flex flex-col">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">Current Order</h3>
            <p class="text-xs text-gray-500">Waiter: {{ $staffUser->name }}</p>
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            <template x-if="cart.length === 0">
                <p class="text-sm text-gray-400 text-center py-8">Tap items to add them.</p>
            </template>
            <template x-for="(item, index) in cart" :key="index">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <div class="text-sm font-semibold text-gray-800" x-text="item.name"></div>
                        <div class="text-xs text-gray-500" x-text="formatCurrency(item.price) + ' x ' + item.quantity"></div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="decrement(index)" class="w-6 h-6 rounded bg-white border border-gray-200 text-gray-600 text-xs">-</button>
                        <span class="text-sm font-semibold w-4 text-center" x-text="item.quantity"></span>
                        <button type="button" @click="increment(index)" class="w-6 h-6 rounded bg-white border border-gray-200 text-gray-600 text-xs">+</button>
                        <button type="button" @click="remove(index)" class="ml-2 text-red-500 hover:text-red-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <div class="p-4 border-t border-gray-100 space-y-3">
            <textarea x-model="notes" placeholder="Order notes (optional)" rows="2"
                class="w-full border-gray-300 rounded-lg text-sm px-3 py-2"></textarea>

            <div class="flex items-center justify-between text-lg font-bold text-gray-800">
                <span>Total</span>
                <span x-text="formatCurrency(total())"></span>
            </div>

            <button type="button" @click="submitOrder()"
                :disabled="cart.length === 0"
                class="w-full px-4 py-3 rounded-xl font-semibold text-white transition-all"
                :class="cart.length ? 'bg-primary hover:bg-blue-700 shadow-lg' : 'bg-gray-300 cursor-not-allowed'">
                Place Order
            </button>
        </div>
    </div>

    <form x-ref="orderForm" method="POST" action="{{ route('pos.orders.store') }}" class="hidden">
        @csrf
        <input type="hidden" name="customer_name" :value="customerName">
        <input type="hidden" name="customer_phone" :value="customerPhone">
        <input type="hidden" name="order_type" :value="orderType">
        <input type="hidden" name="booking_id" :value="bookingId">
        <input type="hidden" name="notes" :value="notes">
        <template x-for="(item, index) in cart" :key="index">
            <div>
                <input type="hidden" :name="`items[${index}][menu_item_id]`" :value="item.id">
                <input type="hidden" :name="`items[${index}][quantity]`" :value="item.quantity">
            </div>
        </template>
    </form>
</div>

<script>
function waiterPos() {
    return {
        activeCategory: null,
        customerName: '',
        customerPhone: '',
        orderType: 'walkin',
        bookingSearch: '',
        showBookingDropdown: false,
        selectedBooking: null,
        bookingId: '',
        notes: '',
        cart: [],
        bookings: @json($activeBookings->map(fn($b) => [
            'id' => $b->id,
            'booking_number' => $b->booking_number,
            'guest_name' => $b->guest_name,
        ])),

        get filteredBookings() {
            if (!this.bookingSearch) return this.bookings;
            const term = this.bookingSearch.toLowerCase();
            return this.bookings.filter(b =>
                b.guest_name.toLowerCase().includes(term) ||
                b.booking_number.toLowerCase().includes(term)
            );
        },

        addItem(item) {
            const existing = this.cart.find(i => i.id === item.id);
            if (existing) {
                existing.quantity++;
            } else {
                this.cart.push({ ...item, quantity: 1 });
            }
        },

        increment(index) { this.cart[index].quantity++; },
        decrement(index) {
            if (this.cart[index].quantity > 1) this.cart[index].quantity--;
            else this.cart.splice(index, 1);
        },
        remove(index) { this.cart.splice(index, 1); },

        total() {
            return this.cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
        },

        selectBooking(booking) {
            this.selectedBooking = booking;
            this.bookingId = booking.id;
            this.bookingSearch = '';
            this.showBookingDropdown = false;
        },

        clearBooking() {
            this.selectedBooking = null;
            this.bookingId = '';
        },

        searchBookings() {
            this.showBookingDropdown = this.bookingSearch.length > 0;
        },

        submitOrder() {
            if (this.orderType === 'guest' && !this.bookingId) {
                alert('Please select a checked-in guest to charge to.');
                return;
            }
            this.$refs.orderForm.submit();
        },

        formatCurrency(value) {
            return new Intl.NumberFormat('en-TZ', { minimumFractionDigits: 0, maximumFractionDigits: 0 })
                .format(Number(value || 0)) + ' TZS';
        },
    };
}
</script>
@endsection
