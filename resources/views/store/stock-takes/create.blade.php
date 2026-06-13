@extends('store.layout')

@section('content')
<div x-data="stockTakeScan()" x-init="init()" class="max-w-5xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Stock-Take Session</h2>
            <p class="text-sm text-gray-500 mt-1">Code: <span class="font-mono font-medium">{{ $stockTake->stock_take_code }}</span></p>
        </div>
        <div class="flex gap-3">
            <form method="POST" action="{{ route('store.stock-takes.cancel', $stockTake) }}" onsubmit="return confirm('Cancel this stock-take?')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl text-sm font-medium">Cancel</button>
            </form>
            <a href="{{ route('store.stock-takes.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium">Exit</a>
        </div>
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
                <div class="text-3xl font-bold text-primary" x-text="totalScanned">0</div>
                <div class="text-xs text-gray-500">Items Scanned</div>
            </div>
        </div>
        <div x-show="scanError" x-cloak class="mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600" x-text="scanError"></div>
        <div x-show="scanSuccess" x-cloak class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-600" x-text="scanSuccess"></div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">Physical Count</h3>
        </div>
        <table class="w-full" x-show="items.length > 0">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Beverage</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Expected</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Physical</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Variance</th>
                    <th class="px-4 py-2 text-center text-xs font-semibold text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <template x-for="item in items" :key="item.id">
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <div class="font-medium text-gray-900" x-text="item.beverage_name"></div>
                            <code class="text-xs text-gray-400 font-mono" x-text="item.barcode"></code>
                        </td>
                        <td class="px-4 py-3 text-center text-sm" x-text="item.expected_quantity"></td>
                        <td class="px-4 py-3 text-center">
                            <input type="number" :value="item.physical_count" min="0"
                                @change="updateCount(item, $event.target.value)"
                                class="w-16 text-center px-2 py-1 border border-gray-300 rounded text-sm">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-semibold text-sm"
                                :class="{
                                    'text-green-600': item.variance === 0,
                                    'text-orange-600': item.variance > 0,
                                    'text-red-600': item.variance < 0,
                                }"
                                x-text="item.variance > 0 ? '+' + item.variance : item.variance"></span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium"
                                :class="{
                                    'bg-green-100 text-green-700': item.variance === 0,
                                    'bg-orange-100 text-orange-700': item.variance > 0,
                                    'bg-red-100 text-red-700': item.variance < 0,
                                }"
                                x-text="item.variance === 0 ? 'Match' : (item.variance > 0 ? 'Surplus' : 'Shortage')"></span>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
        <div x-show="items.length === 0" class="px-6 py-12 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Scan barcodes to begin physical count
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6" x-show="items.length > 0">
        <form method="POST" action="{{ route('store.stock-takes.complete', $stockTake) }}"
            onsubmit="return confirm('Complete this stock-take? Inventory will be adjusted for all variances.')">
            @csrf
            <button type="submit" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition-colors">
                Complete Stock-Take & Adjust Inventory
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function stockTakeScan() {
    return {
        barcode: '',
        items: [],
        totalScanned: 0,
        scanError: '',
        scanSuccess: '',
        stockTakeId: '{{ $stockTake->id }}',

        init() {
            this.$nextTick(() => this.$refs.scanInput?.focus());
            document.addEventListener('click', () => setTimeout(() => this.$refs.scanInput?.focus(), 100));
        },

        async scanBarcode() {
            if (this.barcode.trim().length < 4) return;
            this.scanError = '';
            this.scanSuccess = '';

            try {
                const res = await fetch(`/store/stock-takes/${this.stockTakeId}/scan`, {
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
                        existing.physical_count = data.item.physical_count;
                        existing.variance = data.item.variance;
                    } else {
                        this.items.push(data.item);
                    }
                    this.totalScanned = data.total_scanned;
                    this.scanSuccess = `Scanned: ${data.item.beverage_name} (Count: ${data.item.physical_count})`;
                    setTimeout(() => this.scanSuccess = '', 2000);
                } else {
                    this.scanError = data.message || 'Beverage not found.';
                }
            } catch (e) {
                this.scanError = 'Network error.';
            }

            this.barcode = '';
            this.$refs.scanInput?.focus();
        },

        async updateCount(item, count) {
            try {
                const res = await fetch(`/store/stock-take-items/${item.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ physical_count: parseInt(count) }),
                });
                const data = await res.json();
                item.physical_count = data.physical_count;
                item.variance = data.variance;
            } catch (e) {}
        },
    };
}
</script>
@endpush
@endsection
