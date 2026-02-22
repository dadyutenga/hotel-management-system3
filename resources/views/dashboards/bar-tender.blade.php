{{-- resources/views/dashboards/bar-tender.blade.php --}}
@extends('layouts.app')

@section('title', 'Bar Tender Dashboard - MRK Hotel')
@section('page-title', 'Bar Tender Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-amber-400 to-orange-500 rounded-2xl p-6 mb-8 text-white shadow-xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold mb-2">Welcome, {{ auth()->user()->name }}!</h2>
            <p class="text-amber-100">Your bar overview for today.</p>
        </div>
        <div class="hidden md:block text-right">
            <p class="text-sm text-amber-200">{{ now()->format('l, F d, Y') }}</p>
            <p class="text-3xl font-extrabold">{{ now()->format('h:i A') }}</p>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Available Items</p>
                <p class="text-3xl font-extrabold text-secondary mt-1">{{ $stats['available_items'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Served Today</p>
                <p class="text-3xl font-extrabold text-secondary mt-1">{{ $stats['today_served'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Bar Stock Levels -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-extrabold text-secondary">Bar Stock Levels</h3>
        <a href="{{ route('store.stock.levels') }}" class="text-primary text-sm hover:underline">View all</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-600 font-semibold">Product</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-semibold">Unit</th>
                    <th class="px-4 py-3 text-right text-gray-600 font-semibold">Quantity</th>
                    <th class="px-4 py-3 text-center text-gray-600 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($stockLevels as $level)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium">{{ $level->product->name }}</td>
                    <td class="px-4 py-3 text-gray-500">{{ $level->product->unit }}</td>
                    <td class="px-4 py-3 text-right font-medium">{{ $level->quantity }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($level->quantity <= 0)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-600">Empty</span>
                        @elseif($level->quantity <= ($level->product->reorder_level ?? 0))
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">Low</span>
                        @else
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">OK</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No stock data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
