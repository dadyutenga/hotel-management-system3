@extends('layouts.app')

@section('title', __('general.restaurant.options.title'))
@section('page-title', __('general.restaurant.options.title'))

@section('content')
<div class="space-y-6" x-data="menuOptionsPage()">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
        <h1 class="text-xl font-bold mb-4">{{ __('general.restaurant.options.title') }}</h1>
        <form method="POST" action="{{ route('restaurant.menu.options.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('general.restaurant.fields.option_group_name') }}</label>
                    <input name="name" required class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('general.restaurant.fields.selection_type') }}</label>
                    <select name="selection_type" class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="single">{{ __('general.restaurant.options.single') }}</option>
                        <option value="multiple">{{ __('general.restaurant.options.multiple') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">{{ __('general.restaurant.fields.sort_order') }}</label>
                    <input type="number" name="sort_order" value="0" min="0" class="w-full border-gray-300 rounded px-3 py-2 text-sm">
                </div>
            </div>
            <div class="flex gap-6">
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="hidden" name="is_required" value="0">
                    <input type="checkbox" name="is_required" value="1" class="rounded">
                    {{ __('general.restaurant.options.required') }}
                </label>
                <label class="inline-flex items-center gap-2 text-sm">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded">
                    {{ __('general.active') }}
                </label>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">{{ __('general.restaurant.options.attach_items') }}</label>
                <select multiple name="menu_item_ids[]" class="w-full border-gray-300 rounded px-3 py-2 text-sm h-28">
                    @foreach($menuItems as $menuItem)
                        <option value="{{ $menuItem->id }}">{{ $menuItem->name }} ({{ $menuItem->category->name ?? '—' }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium">{{ __('general.restaurant.options.values') }}</label>
                    <button type="button" @click="addValue()" class="text-xs bg-gray-100 px-3 py-1 rounded">+ {{ __('general.add') }}</button>
                </div>
                <template x-for="(value, idx) in values" :key="idx">
                    <div class="grid grid-cols-12 gap-2 mb-2">
                        <input class="col-span-6 border-gray-300 rounded px-2 py-1 text-sm" :name="'values['+idx+'][label]'" :placeholder="'{{ __('general.restaurant.fields.label') }}'" required>
                        <input class="col-span-3 border-gray-300 rounded px-2 py-1 text-sm" :name="'values['+idx+'][price_delta]'" type="number" step="0.01" min="0" placeholder="0.00" required>
                        <input class="col-span-2 border-gray-300 rounded px-2 py-1 text-sm" :name="'values['+idx+'][sort_order]'" type="number" min="0" :value="idx">
                        <button type="button" @click="removeValue(idx)" class="col-span-1 text-red-600">✕</button>
                    </div>
                </template>
            </div>

            <button class="bg-primary text-white px-4 py-2 rounded text-sm">{{ __('general.create') }}</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-5">
        <h2 class="text-lg font-semibold mb-3">{{ __('general.restaurant.options.existing') }}</h2>
        <div class="space-y-3">
            @forelse($groups as $group)
                <details class="border rounded p-3">
                    <summary class="cursor-pointer flex items-center justify-between">
                        <span class="font-medium">{{ $group->name }}</span>
                        <span class="text-xs text-gray-500">{{ ucfirst($group->selection_type) }} · {{ $group->is_required ? __('general.yes') : __('general.no') }}</span>
                    </summary>
                    <form method="POST" action="{{ route('restaurant.menu.options.update', $group) }}" class="mt-3 space-y-3">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <input name="name" value="{{ $group->name }}" required class="border-gray-300 rounded px-3 py-2 text-sm">
                            <select name="selection_type" class="border-gray-300 rounded px-3 py-2 text-sm">
                                <option value="single" @selected($group->selection_type === 'single')>{{ __('general.restaurant.options.single') }}</option>
                                <option value="multiple" @selected($group->selection_type === 'multiple')>{{ __('general.restaurant.options.multiple') }}</option>
                            </select>
                            <input type="number" name="sort_order" value="{{ $group->sort_order }}" min="0" class="border-gray-300 rounded px-3 py-2 text-sm">
                        </div>
                        <div class="flex gap-6">
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="hidden" name="is_required" value="0">
                                <input type="checkbox" name="is_required" value="1" @checked($group->is_required) class="rounded">
                                {{ __('general.restaurant.options.required') }}
                            </label>
                            <label class="inline-flex items-center gap-2 text-sm">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" @checked($group->is_active) class="rounded">
                                {{ __('general.active') }}
                            </label>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('general.restaurant.options.attach_items') }}</label>
                            <select multiple name="menu_item_ids[]" class="w-full border-gray-300 rounded px-3 py-2 text-sm h-28">
                                @foreach($menuItems as $menuItem)
                                    <option value="{{ $menuItem->id }}" @selected($group->menuItems->contains('id', $menuItem->id))>
                                        {{ $menuItem->name }} ({{ $menuItem->category->name ?? '—' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <p class="text-sm font-medium mb-2">{{ __('general.restaurant.options.values') }}</p>
                            @foreach($group->values as $idx => $value)
                                <div class="grid grid-cols-12 gap-2 mb-2">
                                    <input type="hidden" name="values[{{ $idx }}][id]" value="{{ $value->id }}">
                                    <input class="col-span-5 border-gray-300 rounded px-2 py-1 text-sm" name="values[{{ $idx }}][label]" value="{{ $value->label }}" required>
                                    <input class="col-span-3 border-gray-300 rounded px-2 py-1 text-sm" name="values[{{ $idx }}][price_delta]" type="number" step="0.01" min="0" value="{{ $value->price_delta }}" required>
                                    <input class="col-span-2 border-gray-300 rounded px-2 py-1 text-sm" name="values[{{ $idx }}][sort_order]" type="number" min="0" value="{{ $value->sort_order }}">
                                    <label class="col-span-2 inline-flex items-center gap-1 text-xs">
                                        <input type="hidden" name="values[{{ $idx }}][is_active]" value="0">
                                        <input type="checkbox" name="values[{{ $idx }}][is_active]" value="1" @checked($value->is_active)>
                                        {{ __('general.active') }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <button class="px-3 py-1 bg-gray-800 text-white rounded text-xs">{{ __('general.update') }}</button>
                    </form>
                    <form method="POST" action="{{ route('restaurant.menu.options.destroy', $group) }}" class="mt-3" onsubmit="return confirm('{{ __('general.messages.confirm_delete') }}')">
                        @csrf
                        @method('DELETE')
                        <button class="px-3 py-1 border border-red-300 text-red-700 rounded text-xs">{{ __('general.delete') }}</button>
                    </form>
                </details>
            @empty
                <p class="text-sm text-gray-500">{{ __('general.no_data') }}</p>
            @endforelse
        </div>
    </div>
</div>

<script>
function menuOptionsPage() {
    return {
        values: [{}, {}],
        addValue() { this.values.push({}); },
        removeValue(idx) {
            this.values.splice(idx, 1);
            if (this.values.length === 0) this.values.push({});
        }
    };
}
</script>
@endsection

