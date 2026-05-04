{{-- resources/views/dashboards/waiter.blade.php --}}
@extends('layouts.app')

@section('title', 'Waiter Dashboard')
@section('page-title', 'Waiter Dashboard')

@section('content')
<div class="bg-gradient-to-r from-amber-500 to-orange-600 rounded-2xl p-6 mb-8 text-white shadow-xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold mb-2">{{ __('dashboard.welcome.greeting', ['name' => auth()->user()->name]) }}</h2>
            <p class="text-amber-100">Manage restaurant orders, tables, and serve guests efficiently.</p>
        </div>
        <div class="hidden md:block text-right">
            <p id="liveDate" class="text-sm text-amber-200"></p>
            <p id="liveTime" class="text-3xl font-extrabold"></p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Today's Orders</p>
                <p class="text-3xl font-extrabold text-blue-600 mt-1">{{ $stats['today_orders'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Pending Orders</p>
                <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ $stats['pending_orders'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Served Today</p>
                <p class="text-3xl font-extrabold text-green-600 mt-1">{{ $stats['served_today'] }}</p>
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
                <p class="text-sm font-medium text-gray-500">Active Tables</p>
                <p class="text-3xl font-extrabold text-purple-600 mt-1">{{ $stats['occupied_tables'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-extrabold text-gray-800">Today's Orders</h3>
            <a href="{{ route('restaurant.orders.index') }}" class="text-primary text-sm hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-gray-500 border-b">
                        <th class="pb-2 font-medium">Order #</th>
                        <th class="pb-2 font-medium">Table</th>
                        <th class="pb-2 font-medium">Type</th>
                        <th class="pb-2 font-medium">Status</th>
                        <th class="pb-2 font-medium text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentOrders as $order)
                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                        <td class="py-2">
                            <a href="{{ route('restaurant.orders.show', $order) }}" class="text-primary font-medium hover:underline">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td class="py-2 text-gray-600">{{ $order->table?->table_number ?? '—' }}</td>
                        <td class="py-2">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $order->order_type === 'guest' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($order->order_type) }}
                            </span>
                        </td>
                        <td class="py-2">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $order->status === 'settled' ? 'bg-green-100 text-green-700' : ($order->status === 'open' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="py-2 text-right font-medium">@currency($order->total, 'TZS')</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-400">No orders today yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-gray-800 mb-4">Quick Actions</h3>
        <div class="space-y-3">
            <a href="{{ route('restaurant.orders.create') }}" class="flex items-center gap-3 p-3 bg-green-50 rounded-xl hover:bg-green-100 transition">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">New Order</p>
                    <p class="text-xs text-gray-500">Take a guest food/drink order</p>
                </div>
            </a>
            <a href="{{ route('restaurant.pos') }}" class="flex items-center gap-3 p-3 bg-blue-50 rounded-xl hover:bg-blue-100 transition">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">POS Screen</p>
                    <p class="text-xs text-gray-500">Quick walk-in and folio sales</p>
                </div>
            </a>
            <a href="{{ route('restaurant.tables.index') }}" class="flex items-center gap-3 p-3 bg-purple-50 rounded-xl hover:bg-purple-100 transition">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                </div>
                <div>
                    <p class="font-semibold text-gray-800 text-sm">View Tables</p>
                    <p class="text-xs text-gray-500">Manage table status</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
