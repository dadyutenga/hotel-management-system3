@extends('layouts.app')

@section('title', __('bartender.titles.stock'))
@section('page-title', __('bartender.titles.stock'))

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-gray-800">{{ $bar->name }}</h2>
            <p class="text-xs text-gray-500">{{ __('bartender.messages.read_only_stock') }}</p>
        </div>
        <form class="flex gap-2" method="GET">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('bartender.placeholders.search_item_or_sku') }}" class="border-gray-300 rounded-lg text-sm px-3 py-2">
            <button class="px-3 py-2 text-sm rounded-lg bg-gray-100 hover:bg-gray-200">{{ __('bartender.actions.filter') }}</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.drink_item') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.sku') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('bartender.fields.current_qty') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.unit') }}</th>
                    <th class="px-4 py-3 text-right">{{ __('bartender.fields.reorder_level') }}</th>
                    <th class="px-4 py-3 text-left">{{ __('bartender.fields.last_updated') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($levels as $level)
                    <tr>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $level->product->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $level->product->sku }}</td>
                        <td class="px-4 py-3 text-right font-semibold {{ $level->quantity <= 0 ? 'text-red-600' : 'text-gray-800' }}">{{ number_format((float)$level->quantity, 3) }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $level->product->unit }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ $level->product->reorder_level }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ optional($level->updated_at)->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">{{ __('bartender.messages.no_stock') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $levels->links() }}</div>
</div>
@endsection
