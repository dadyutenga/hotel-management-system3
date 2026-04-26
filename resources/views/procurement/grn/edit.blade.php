{{-- resources/views/procurement/grn/edit.blade.php --}}
@php use App\Helpers\CurrencyHelper; @endphp
@extends('layouts.app')

@section('title', 'Edit GRN')
@section('page-title', 'Goods Received Notes')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-secondary">Edit Goods Received Note</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $goodsReceivedNote->grn_number }} - {{ $goodsReceivedNote->supplierName }}</p>
        </div>

        <form method="POST" action="{{ route('procurement.grn.update', $goodsReceivedNote) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="received_date" class="block text-sm font-medium text-gray-700 mb-2">Received Date <span class="text-red-500">*</span></label>
                        <input type="date" name="received_date" id="received_date" value="{{ old('received_date', optional($goodsReceivedNote->received_date)->format('Y-m-d')) }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label for="delivery_vehicle" class="block text-sm font-medium text-gray-700 mb-2">Vehicle Plate</label>
                        <input type="text" name="delivery_vehicle" id="delivery_vehicle" value="{{ old('delivery_vehicle', $goodsReceivedNote->delivery_vehicle) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label for="driver_name" class="block text-sm font-medium text-gray-700 mb-2">Driver Name</label>
                        <input type="text" name="driver_name" id="driver_name" value="{{ old('driver_name', $goodsReceivedNote->driver_name) }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    </div>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="2" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">{{ old('notes', $goodsReceivedNote->notes) }}</textarea>
                </div>

                <div>
                    <label for="receipt" class="block text-sm font-medium text-gray-700 mb-2">Receipt (optional replacement)</label>
                    <input type="file" name="receipt" id="receipt" accept=".pdf,.jpg,.jpeg,.png" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary">
                    <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max: 5MB)</p>
                    @if($goodsReceivedNote->receipt_path)
                    <a href="{{ Storage::url($goodsReceivedNote->receipt_path) }}" target="_blank" class="inline-block mt-2 text-sm text-primary hover:text-blue-700 font-semibold">View Current Receipt</a>
                    @endif
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <h3 class="text-lg font-semibold text-secondary mb-4">Received Items</h3>
                    <div class="space-y-3">
                        @foreach($goodsReceivedNote->items as $idx => $item)
                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                            <input type="hidden" name="items[{{ $idx }}][id]" value="{{ $item->id }}">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                                <div class="md:col-span-4">
                                    <div class="text-sm font-semibold text-secondary">{{ $item->item_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $item->product?->sku ?? 'Manual entry' }}</div>
                                </div>
                                <div class="md:col-span-2">
                                    <div class="text-xs text-gray-500 mb-1">Unit</div>
                                    <div class="text-sm text-secondary">{{ $item->unit }}</div>
                                </div>
                                <div class="md:col-span-2">
                                    <div class="text-xs text-gray-500 mb-1">Ordered</div>
                                    <div class="text-sm text-secondary">{{ number_format((float) $item->quantity_ordered, 2) }}</div>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs text-gray-500 mb-1">Received *</label>
                                    <input type="number" step="0.001" min="0.001" name="items[{{ $idx }}][quantity_received]" value="{{ old("items.$idx.quantity_received", (float) $item->quantity_received) }}" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary item-received">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-xs text-gray-500 mb-1">Unit Price *</label>
                                    <input type="number" step="0.01" min="0.01" name="items[{{ $idx }}][unit_price]" value="{{ old("items.$idx.unit_price", (float) $item->unit_price) }}" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary item-price">
                                </div>
                            </div>
                            <div class="mt-2">
                                <input type="text" name="items[{{ $idx }}][notes]" value="{{ old("items.$idx.notes", $item->notes) }}" placeholder="Item notes (damages, shortages, etc.)" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Subtotal</div>
                            <div class="text-2xl font-bold text-secondary" id="subtotal-display">{{ CurrencyHelper::formatCurrency((float) $goodsReceivedNote->subtotal) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Tax (18%)</div>
                            <div class="text-2xl font-bold text-secondary" id="tax-display">{{ CurrencyHelper::formatCurrency((float) $goodsReceivedNote->tax_amount) }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600 mb-1">Grand Total</div>
                            <div class="text-3xl font-extrabold text-primary" id="total-display">{{ CurrencyHelper::formatCurrency((float) $goodsReceivedNote->grand_total) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('procurement.grn.show', $goodsReceivedNote) }}" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-gradient-to-r from-primary to-blue-600 rounded-lg hover:shadow-lg">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
const currencySymbol = '{{ CurrencyHelper::getCurrencySymbol() }}';
const currencyPosition = '{{ CurrencyHelper::CURRENCIES[CurrencyHelper::getDefaultCurrency()]["position"] ?? "before" }}';

function formatMoney(amount) {
    const formatted = amount.toFixed(2);
    return currencyPosition === 'before' ? currencySymbol + formatted : formatted + ' ' + currencySymbol;
}

function calculateGrandTotal() {
    let subtotal = 0;

    document.querySelectorAll('.item-received').forEach((qtyInput) => {
        const row = qtyInput.closest('.bg-gray-50');
        const priceInput = row.querySelector('.item-price');
        const qty = parseFloat(qtyInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        subtotal += qty * price;
    });

    const tax = subtotal * 0.18;
    const total = subtotal + tax;

    document.getElementById('subtotal-display').textContent = formatMoney(subtotal);
    document.getElementById('tax-display').textContent = formatMoney(tax);
    document.getElementById('total-display').textContent = formatMoney(total);
}

document.addEventListener('input', function (event) {
    if (event.target.classList.contains('item-received') || event.target.classList.contains('item-price')) {
        calculateGrandTotal();
    }
});
</script>
@endsection
