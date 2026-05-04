{{-- resources/views/restaurant/menu/items/index.blade.php --}}
@extends('layouts.app')

@section('title', __('general.nav.menu'))
@section('page-title', __('general.nav.menu'))

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-extrabold text-gray-800">{{ __('general.nav.menu') }}</h1>
    @if(auth()->user()->hasAnyRole(['restaurant_manager','manager','admin']))
    <a href="{{ route('restaurant.menu.create') }}"
       class="bg-primary text-white px-4 py-2 rounded text-sm hover:opacity-90">
        + {{ __('general.restaurant.menu.new_item') }}
    </a>
    @endif
</div>

{{-- Location filter --}}
<div class="flex gap-3 mb-6">
    <a href="{{ route('restaurant.menu.index') }}"
       class="px-4 py-2 rounded text-sm border {{ !request('location_id') ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
        {{ __('general.all_time') }}
    </a>
    @foreach($categories->pluck('location')->unique('id') as $loc)
    <a href="{{ route('restaurant.menu.index', ['location_id' => $loc->id]) }}"
       class="px-4 py-2 rounded text-sm border {{ request('location_id') === $loc->id ? 'bg-primary text-white border-primary' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
        {{ $loc->name }}
    </a>
    @endforeach
</div>

@forelse($categories as $category)
<div class="mb-8">
    <div class="flex items-center gap-3 mb-4">
        <h2 class="text-lg font-extrabold text-gray-700">{{ $category->name }}</h2>
        <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded-full">{{ $category->location->name }}</span>
        <span class="text-xs text-gray-400">{{ $category->menuItems->count() }} {{ __('general.restaurant.menu.items') }}</span>
    </div>

    @if($category->menuItems->count())
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($category->menuItems as $item)
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 {{ !$item->is_available ? 'opacity-60' : '' }}">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $item->name }}</h3>
                    @if($item->description)
                    <p class="text-xs text-gray-500 mt-1">{{ Str::limit($item->description, 80) }}</p>
                    @endif
                </div>
                <span class="text-lg font-bold text-primary">@currency($item->selling_price, 'TZS')</span>
            </div>

            @if($item->ingredients->count())
            <div class="mt-2 pt-2 border-t">
                     <p class="text-xs text-gray-400 mb-1">{{ __('general.restaurant.menu.ingredients') }}:</p>
                <div class="flex flex-wrap gap-1">
                    @foreach($item->ingredients as $ing)
                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
                        {{ $ing->product->name ?? 'Unknown' }} ({{ $ing->quantity }} {{ $ing->unit }})
                    </span>
                    @endforeach
                </div>
            </div>
            @endif

            <div class="mt-3 flex items-center justify-between">
                <span class="text-xs px-2 py-0.5 rounded-full {{ $item->is_available ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                     {{ $item->is_available ? __('general.restaurant.status.available') : __('general.restaurant.status.unavailable') }}
                </span>
                @if(auth()->user()->hasAnyRole(['restaurant_manager','manager','admin']))
                <div class="flex gap-2">
                    <a href="{{ route('restaurant.menu.edit', $item) }}" class="text-xs text-blue-600 hover:underline">{{ __('general.edit') }}</a>
                    <form method="POST" action="{{ route('restaurant.menu.destroy', $item) }}" onsubmit="return confirm('{{ __('general.restaurant.messages.remove_item_confirm') }}')">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-600 hover:underline">{{ __('general.remove') }}</button>
                    </form>
                </div>
                @endif
            </div>
            @if($item->optionGroups->count())
            <div class="mt-2 pt-2 border-t">
                <p class="text-xs text-gray-400 mb-1">{{ __('general.restaurant.options.title') }}:</p>
                <div class="flex flex-wrap gap-1">
                    @foreach($item->optionGroups as $group)
                    <span class="text-xs bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded">
                        {{ $group->name }} ({{ $group->selection_type }})
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <p class="text-sm text-gray-400">{{ __('general.no_data') }}</p>
    @endif
</div>
@empty
<div class="text-center py-12 text-gray-400">
    <p>{{ __('general.no_data') }}</p>
</div>
@endforelse
@endsection
