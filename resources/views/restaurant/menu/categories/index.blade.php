@extends('restaurant.layout')

@section('title', __('general.restaurant.categories.title'))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-5">
        <h1 class="text-xl font-bold text-gray-800 mb-4">{{ __('general.restaurant.categories.title') }}</h1>
        <form method="POST" action="{{ route('restaurant.menu.categories.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1">{{ __('general.restaurant.fields.name') }}</label>
                <input name="name" required class="w-full border-gray-300 rounded px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">{{ __('general.restaurant.fields.section') }}</label>
                <select name="location_id" required class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="">{{ __('general.restaurant.placeholders.select_section') }}</option>
                    @foreach($locations as $location)
                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('general.restaurant.fields.sort_order') }}</label>
                    <input type="number" name="sort_order" value="0" min="0" class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                </div>
                <div class="flex items-center gap-2 mt-7">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded">
                    <span class="text-sm">{{ __('general.active') }}</span>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">{{ __('general.description') }}</label>
                <textarea name="description" rows="2" class="w-full border-gray-300 rounded px-3 py-2 text-sm"></textarea>
            </div>
            <button class="bg-primary text-white px-4 py-2 rounded text-sm">{{ __('general.create') }}</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow p-5">
        <h2 class="text-lg font-semibold mb-4">{{ __('general.restaurant.categories.existing') }}</h2>
        <div class="space-y-3 max-h-[70vh] overflow-y-auto">
            @forelse($categories as $category)
                <form method="POST" action="{{ route('restaurant.menu.categories.update', $category) }}" class="border rounded p-3 space-y-2">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-2 gap-2">
                        <input name="name" value="{{ $category->name }}" class="border-gray-300 rounded px-2 py-1 text-sm" required>
                        <select name="location_id" class="border-gray-300 rounded px-2 py-1 text-sm" required>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" @selected($location->id === $category->location_id)>{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <input type="number" name="sort_order" value="{{ $category->sort_order }}" min="0" class="border-gray-300 rounded px-2 py-1 text-sm">
                        <label class="inline-flex items-center gap-2 text-sm">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" @checked($category->is_active) class="rounded">
                            {{ __('general.active') }}
                        </label>
                    </div>
                    <textarea name="description" rows="2" class="w-full border-gray-300 rounded px-2 py-1 text-sm">{{ $category->description }}</textarea>
                    <div class="flex gap-2">
                        <button class="px-3 py-1 bg-gray-800 text-white rounded text-xs">{{ __('general.update') }}</button>
                    </div>
                </form>
                <form method="POST" action="{{ route('restaurant.menu.categories.destroy', $category) }}" onsubmit="return confirm('{{ __('general.messages.confirm_delete') }}')" class="mt-1">
                    @csrf
                    @method('DELETE')
                    <button class="px-3 py-1 border border-red-300 text-red-700 rounded text-xs">
                        {{ __('general.delete') }}
                    </button>
                </form>
            @empty
                <p class="text-sm text-gray-500">{{ __('general.no_data') }}</p>
            @endforelse
        </div>
    </div>
</div>
@endsection

