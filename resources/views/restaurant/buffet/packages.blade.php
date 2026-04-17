@extends('restaurant.layout')

@section('title', __('general.restaurant.buffet.nav'))

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">{{ __('general.restaurant.buffet.nav') }}</h1>
        <a href="{{ route('restaurant.buffet.create') }}" class="px-4 py-2 bg-primary text-white rounded text-sm">
            {{ __('general.restaurant.buffet.new_sale') }}
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-5">
        <h2 class="font-semibold text-gray-700 mb-3">{{ __('general.restaurant.buffet.package_setup') }}</h2>
        <form method="POST" action="{{ route('restaurant.buffet.packages.store') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3">
            @csrf
            <input name="name" class="border-gray-300 rounded px-3 py-2 text-sm md:col-span-2" placeholder="{{ __('general.restaurant.buffet.fields.package_name') }}" required>
            <input type="number" step="0.01" min="0.01" name="adult_price" class="border-gray-300 rounded px-3 py-2 text-sm" placeholder="{{ __('general.restaurant.buffet.fields.adult_price') }}" required>
            <input type="number" step="0.01" min="0" name="child_price" class="border-gray-300 rounded px-3 py-2 text-sm" placeholder="{{ __('general.restaurant.buffet.fields.child_price') }}">
            <input type="time" name="start_time" class="border-gray-300 rounded px-3 py-2 text-sm">
            <input type="time" name="end_time" class="border-gray-300 rounded px-3 py-2 text-sm">
            <div class="md:col-span-6 grid grid-cols-2 md:grid-cols-7 gap-2">
                @foreach(['monday','tuesday','wednesday','thursday','friday','saturday','sunday'] as $day)
                    <label class="text-xs inline-flex items-center gap-1">
                        <input type="checkbox" name="available_days[]" value="{{ $day }}">
                        {{ __('general.days.' . $day) }}
                    </label>
                @endforeach
            </div>
            <div class="md:col-span-6">
                <button class="px-4 py-2 bg-green-600 text-white rounded text-sm">{{ __('general.create') }}</button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">{{ __('general.name') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('general.restaurant.buffet.fields.adult_price') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('general.restaurant.buffet.fields.child_price') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('general.restaurant.buffet.fields.schedule') }}</th>
                    <th class="px-4 py-2 text-left">{{ __('general.status') }}</th>
                    <th class="px-4 py-2"></th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($packages as $package)
                    <tr>
                        <td class="px-4 py-2">{{ $package->name }}</td>
                        <td class="px-4 py-2">{{ number_format($package->adult_price, 0) }}</td>
                        <td class="px-4 py-2">{{ number_format($package->child_price, 0) }}</td>
                        <td class="px-4 py-2 text-xs text-gray-600">
                            {{ collect($package->available_days ?? [])->map(fn($d) => __('general.days.' . strtolower($d)))->implode(', ') ?: '-' }}
                            ({{ $package->start_time ?? '--:--' }} - {{ $package->end_time ?? '--:--' }})
                        </td>
                        <td class="px-4 py-2">{{ $package->is_active ? __('general.active') : __('general.inactive') }}</td>
                        <td class="px-4 py-2 text-right">
                            @if($package->is_active)
                                <form method="POST" action="{{ route('restaurant.buffet.packages.deactivate', $package) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 text-xs">{{ __('general.delete') }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

