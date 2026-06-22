@extends('store.layout')

@section('content')
<div x-data="receivingScan()" x-init="init()" class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">New Stock Receiving</h2>
            <p class="text-sm text-gray-500 mt-1">Code: <span class="font-mono font-medium">{{ $receiving->receiving_code }}</span></p>
        </div>
        <a href="{{ route('store.receivings.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium">Cancel</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-500 mb-1">SCAN BARCODE</label>
                <input type="text" x-ref="scanInput" x-model="barcode" @keydown.enter.prevent="scanBarcode()"
                    class="w-full px-4 py-3 border-2 border-primary/30 rounded-xl text-lg font-mono focus:ring-2 focus:ring-primary/20 focus:border-primary"
                    placeholder="Ready to scan..." autofocus>
            </div>
            <div class="text-center px-6">
                <div class="text-3xl font-bold text-primary" x-text="totalItems">0</div>
                <div class="text-xs text-gray-500">Items Scanned</div>
            </div>
        </div>
        <div x-show="scanError" x-cloak class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600" x-text="scanError"></div>
        <div x-show="scanSuccess" x-cloak class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-600" x-text="scanSuccess"></div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Scanned Items</h3>
        </div>
        <table class="w-full" x-show="items.length > 0">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Beverage</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Barcode</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Qty</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Unit Price</th>
                    <th class="px-4 py-2 text-right text-xs font-semibold text-gray-500">Total</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-for="item in items" :key="item.id">
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900" x-text="item.beverage_name"></td>
                        <td class="px-4 py-3"><code class="text-xs bg-gray-100 px-2 py-0.5 rounded font-mono" x-text="item.barcode"></code></td>
                        <td class="px-4 py-3 text-center">
                            <input type="number" :value="item.quantity" min="1"
                                @change="updateQuantity(item, $event.target.value)"
                                class="w-16 text-center px-2 py-1 border border-gray-300 rounded text-sm">
                        </td>
                        <td class="px-4 py-3 text-right text-sm" x-text="parseFloat(item.unit_buying_price).toFixed(2)"></td>
                        <td class="px-4 py-3 text-right text-sm font-medium" x-text="(item.quantity * parseFloat(item.unit_buying_price)).toFixed(2)"></td>
                        <td class="px-4 py-3 text-center">
                            <button @click="removeItem(item)" class="text-red-500 hover:text-red-700 text-xs font-medium">Remove</button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <div x-show="items.length === 0" class="px-6 py-12 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
            Scan barcodes to add items
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-show="items.length > 0">
        <h3 class="font-semibold text-gray-800 mb-4">Confirm Receiving</h3>
        <form method="POST" action="{{ route('store.receivings.confirm', $receiving) }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier (optional)</label>
                    <select name="supplier_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                        <option value="">No supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                    <input type="text" name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Any notes...">
                </div>
            </div>
            <button type="submit" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition-colors"
                onclick="return confirm('Confirm this receiving? Stock will be updated immediately.')">
                Confirm & Post to Inventory
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function receivingScan() {
    return {
        barcode: '',
        items: [],
        totalItems: 0,
        scanError: '',
        scanSuccess: '',
        receivingId: '{{ $receiving->id }}',

        init() {
            this.$nextTick(() => {
                this.$refs.scanInput?.focus();
            });
            document.addEventListener('click', () => {
                setTimeout(() => this.$refs.scanInput?.focus(), 100);
            });
        },

        async scanBarcode() {
            if (this.barcode.trim().length < 4) return;

            this.scanError = '';
            this.scanSuccess = '';

            try {
                const res = await fetch(`/store/receivings/${this.receivingId}/scan`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ barcode: this.barcode.trim() }),
                });

                const data = await res.json();

                if (data.success) {
                    const existing = this.items.find(i => i.id === data.item.id);
                    if (existing) {
                        existing.quantity = data.item.quantity;
                    } else {
                        this.items.push(data.item);
                    }
                    this.totalItems = data.total_items;
                    this.scanSuccess = `Scanned: ${data.item.beverage_name} (Qty: ${data.item.quantity})`;
                    setTimeout(() => this.scanSuccess = '', 2000);
                } else {
                    this.scanError = data.message || 'Beverage not found for this barcode.';
                }
            } catch (e) {
                this.scanError = 'Network error. Please try again.';
            }

            this.barcode = '';
            this.$refs.scanInput?.focus();
        },

        async updateQuantity(item, qty) {
            try {
                await fetch(`/store/receiving-items/${item.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ quantity: parseInt(qty) }),
                });
                item.quantity = parseInt(qty);
                this.recalcTotal();
            } catch (e) {}
        },

        async removeItem(item) {
            if (!confirm('Remove this item?')) return;
            try {
                await fetch(`/store/receiving-items/${item.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                this.items = this.items.filter(i => i.id !== item.id);
                this.recalcTotal();
            } catch (e) {}
        },

        recalcTotal() {
            this.totalItems = this.items.reduce((sum, i) => sum + i.quantity, 0);
        },
    };
}
</script>
@endpush
@endsection
