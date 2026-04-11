@extends('layouts.app')

@section('title', __('bartender.titles.damage_create'))
@section('page-title', __('bartender.titles.damage_create'))

@section('content')
<div class="max-w-2xl mx-auto bg-white rounded-xl border border-gray-100 shadow-sm p-5">
    <form method="POST" action="{{ route('bartender.damage.store') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm text-gray-600 mb-1">{{ __('bartender.fields.stock_item') }}</label>
            <select name="product_id" required class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
                <option value="">{{ __('bartender.fields.select_drink_product') }}</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('bartender.fields.quantity_damaged') }}</label>
                <input type="number" step="0.001" min="0.001" name="quantity" required class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">{{ __('bartender.fields.reason') }}</label>
                <select name="reason" required class="w-full border-gray-300 rounded-lg text-sm px-3 py-2">
                    <option value="spillage">{{ __('bartender.reasons.spillage') }}</option>
                    <option value="breakage">{{ __('bartender.reasons.breakage') }}</option>
                    <option value="expired">{{ __('bartender.reasons.expired') }}</option>
                    <option value="other">{{ __('bartender.reasons.other') }}</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">{{ __('bartender.fields.notes_optional') }}</label>
            <textarea name="notes" rows="3" class="w-full border-gray-300 rounded-lg text-sm px-3 py-2"></textarea>
        </div>

        <div>
            <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">{{ __('bartender.actions.submit_damage') }}</button>
        </div>
    </form>
</div>
@endsection
