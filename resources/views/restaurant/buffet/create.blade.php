@extends('layouts.app')

@section('title', __('general.restaurant.buffet.new_sale'))
@section('page-title', __('general.restaurant.buffet.new_sale'))

@section('content')
<div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
    <h1 class="text-xl font-extrabold mb-4">{{ __('general.restaurant.buffet.new_sale') }}</h1>

    <form method="POST" action="{{ route('restaurant.buffet.store') }}" class="space-y-4" x-data="{ saleType: 'walkin' }">
        @csrf
        <div>
            <label class="block text-sm mb-1">{{ __('general.restaurant.buffet.fields.package_name') }}</label>
            <select name="buffet_package_id" required class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                <option value="">{{ __('general.restaurant.placeholders.select_item') }}</option>
                @foreach($packages as $package)
                    <option value="{{ $package->id }}">{{ $package->name }} ({{ number_format($package->adult_price, 0) }} / {{ number_format($package->child_price, 0) }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm mb-1">{{ __('general.restaurant.buffet.fields.sale_type') }}</label>
            <select name="sale_type" x-model="saleType" class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                <option value="walkin">{{ __('general.restaurant.buffet.sale_type.walkin') }}</option>
                <option value="booking">{{ __('general.restaurant.buffet.sale_type.booking') }}</option>
            </select>
        </div>

        <div x-show="saleType === 'booking'">
            <label class="block text-sm mb-1">{{ __('general.restaurant.buffet.fields.booking') }}</label>
            <select name="booking_id" class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                <option value="">{{ __('general.restaurant.placeholders.select_item') }}</option>
                @foreach($bookings as $booking)
                    <option value="{{ $booking->id }}">{{ $booking->booking_number }} - {{ $booking->guest_display_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="block text-sm mb-1">{{ __('general.restaurant.buffet.fields.adults') }}</label>
                <input type="number" min="0" name="adults_count" value="1" class="w-full border-gray-300 rounded px-3 py-2 text-sm" required>
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('general.restaurant.buffet.fields.children') }}</label>
                <input type="number" min="0" name="children_count" value="0" class="w-full border-gray-300 rounded px-3 py-2 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm mb-1">{{ __('general.notes') }}</label>
            <textarea name="notes" class="w-full border-gray-300 rounded px-3 py-2 text-sm"></textarea>
        </div>

        <button class="px-4 py-2 bg-primary text-white rounded text-sm">{{ __('general.create') }}</button>
    </form>
</div>
@endsection

