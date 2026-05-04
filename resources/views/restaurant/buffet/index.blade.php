@extends('layouts.app')

@section('title', __('general.restaurant.buffet.sales'))
@section('page-title', __('general.restaurant.buffet.sales'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-extrabold text-gray-800">{{ __('general.restaurant.buffet.sales') }}</h1>
        <a href="{{ route('restaurant.buffet.create') }}" class="bg-primary text-white px-4 py-2 rounded text-sm">
            {{ __('general.restaurant.buffet.new_sale') }}
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
        <form method="GET" class="flex gap-3 items-end">
            <div>
                <label class="text-xs text-gray-500">{{ __('general.date') }}</label>
                <input type="date" name="from" value="{{ request('from') }}" class="border-gray-300 rounded px-2 py-1 text-sm">
            </div>
            <div>
                <label class="text-xs text-gray-500">{{ __('general.date') }}</label>
                <input type="date" name="to" value="{{ request('to') }}" class="border-gray-300 rounded px-2 py-1 text-sm">
            </div>
            <div>
                <label class="text-xs text-gray-500">{{ __('general.status') }}</label>
                <select name="status" class="border-gray-300 rounded px-2 py-1 text-sm">
                    <option value="">{{ __('general.view_all') }}</option>
                    @foreach(['pending','charged','settled','cancelled'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <button class="bg-gray-800 text-white px-3 py-1 rounded text-sm">{{ __('general.filter') }}</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left w-10"></th>
                    <th class="px-4 py-2 text-left">{{ __('general.restaurant.buffet.fields.sale_no') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('general.restaurant.buffet.fields.package_name') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('general.restaurant.buffet.fields.sale_type') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('general.restaurant.buffet.fields.pax') }}</th>
                    <th class="px-4 py-2 text-right">{{ __('general.total') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('general.status') }}</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">
                            @if($sale->package?->hasMedia('buffet_image'))
                                <img src="{{ $sale->package->getFirstMediaUrl('buffet_image', 'thumb') }}" class="w-8 h-8 rounded-lg object-cover border">
                            @endif
                        </td>
                        <td class="px-4 py-2 font-mono text-xs">{{ $sale->sale_number }}</td>
                        <td class="px-4 py-2">{{ $sale->package_name_snapshot }}</td>
                        <td class="px-4 py-2">{{ __('general.restaurant.buffet.sale_type.' . $sale->sale_type) }}</td>
                        <td class="px-4 py-2">{{ $sale->adults_count }}A / {{ $sale->children_count }}C</td>
                        <td class="px-4 py-2 text-right">@currency($sale->total_amount, 'TZS')</td>
                        <td class="px-4 py-2">{{ ucfirst($sale->status) }}</td>
                        <td class="px-4 py-2 text-right">
                            <a href="{{ route('restaurant.buffet.show', $sale) }}" class="text-primary text-xs">{{ __('general.view') }}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div>{{ $sales->withQueryString()->links() }}</div>
</div>
@endsection

