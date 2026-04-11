@extends('layouts.app')

@section('title', 'Bar Stock View')
@section('page-title', 'Bar Stock View')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-gray-800">{{ $bar->name }}</h2>
            <p class="text-xs text-gray-500">Read-only stock levels for bartender role</p>
        </div>
        <form class="flex gap-2" method="GET">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search item or SKU" class="border-gray-300 rounded-lg text-sm px-3 py-2">
            <button class="px-3 py-2 text-sm rounded-lg bg-gray-100 hover:bg-gray-200">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">Drink Item</th>
                    <th class="px-4 py-3 text-left">SKU</th>
                    <th class="px-4 py-3 text-right">Current Qty</th>
                    <th class="px-4 py-3 text-left">Unit</th>
                    <th class="px-4 py-3 text-right">Reorder Level</th>
                    <th class="px-4 py-3 text-left">Last Updated</th>
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
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">No bar stock records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $levels->links() }}</div>
</div>
@endsection
