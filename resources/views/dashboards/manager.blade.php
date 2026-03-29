{{-- resources/views/dashboards/manager.blade.php --}}
@extends('layouts.app')

@section('title', 'Manager Dashboard')
@section('page-title', 'Manager Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-indigo-500 to-indigo-700 rounded-2xl p-6 mb-8 text-white shadow-xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold mb-2">Welcome, {{ auth()->user()->name }}!</h2>
            <p class="text-indigo-100">Here's your management overview for today.</p>
        </div>
        <div class="hidden md:block text-right">
            <p class="text-sm text-indigo-200">{{ now()->format('l, F d, Y') }}</p>
            <p class="text-3xl font-extrabold">{{ now()->format('h:i A') }}</p>
        </div>
    </div>
</div>

<!-- Top Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Pending Approvals</p>
                <p class="text-3xl font-extrabold text-red-600 mt-1">{{ $stats['pending_approvals'] ?? 0 }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Rooms</p>
                <p class="text-3xl font-extrabold text-secondary mt-1">{{ $stats['total_rooms'] }}</p>
                <p class="text-xs text-green-600 font-medium mt-1">{{ $stats['active_rooms'] }} active</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Active Staff</p>
                <p class="text-3xl font-extrabold text-secondary mt-1">{{ $stats['total_users'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Reservations</p>
                <p class="text-3xl font-extrabold text-secondary mt-1">{{ $stats['total_reservations'] }}</p>
                <p class="text-xs text-yellow-600 font-medium mt-1">{{ $stats['pending_reservations'] }} pending</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Pending Approvals Section -->
@if(isset($pendingApprovals) && $pendingApprovals->count() > 0)
<div class="bg-white rounded-2xl shadow-lg border border-red-200 p-6 mb-8">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-extrabold text-red-600 flex items-center gap-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            Pending Approvals
        </h3>
        <a href="{{ route('procurement.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-semibold flex items-center gap-1">
            View All
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs font-semibold text-red-600 uppercase tracking-wider border-b border-red-100">
                    <th class="pb-3 pr-4">Request #</th>
                    <th class="pb-3 pr-4">Requester</th>
                    <th class="pb-3 pr-4">Location</th>
                    <th class="pb-3 pr-4">Date</th>
                    <th class="pb-3">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($pendingApprovals as $request)
                <tr class="hover:bg-red-50/50 transition-colors">
                    <td class="py-3 pr-4">
                        <span class="font-semibold text-secondary">{{ $request->request_number ?? 'N/A' }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-sm text-gray-600">{{ $request->requester->name ?? 'N/A' }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-sm text-gray-600">{{ $request->location->name ?? 'N/A' }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-sm text-gray-600">{{ $request->created_at->format('M d, Y') }}</span>
                    </td>
                    <td class="py-3">
                        <a href="{{ route('procurement.internal-usage.show', $request) }}" class="text-indigo-600 hover:text-indigo-700 font-semibold text-sm">Review</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Room Status & Revenue -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Occupancy Rate -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Occupancy Rate</h3>
        <div class="flex items-center justify-center">
            <div class="relative w-40 h-40">
                <svg class="transform -rotate-90 w-40 h-40">
                    <circle cx="80" cy="80" r="70" stroke="#e5e7eb" stroke-width="10" fill="none" />
                    <circle cx="80" cy="80" r="70" stroke="#4f46e5" stroke-width="10" fill="none"
                            stroke-dasharray="{{ 2 * 3.14159 * 70 }}"
                            stroke-dashoffset="{{ 2 * 3.14159 * 70 * (1 - $stats['occupancy_rate'] / 100) }}"
                            stroke-linecap="round" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-3xl font-extrabold text-secondary">{{ $stats['occupancy_rate'] }}%</span>
                </div>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-2 text-sm">
            <div class="text-center p-3 bg-green-50 rounded-xl">
                <p class="font-bold text-green-700">{{ $stats['available_rooms'] }}</p>
                <p class="text-green-600 text-xs">Available</p>
            </div>
            <div class="text-center p-3 bg-red-50 rounded-xl">
                <p class="font-bold text-red-700">{{ $stats['occupied_rooms'] }}</p>
                <p class="text-red-600 text-xs">Occupied</p>
            </div>
            <div class="text-center p-3 bg-blue-50 rounded-xl">
                <p class="font-bold text-blue-700">{{ $stats['reserved_rooms'] }}</p>
                <p class="text-blue-600 text-xs">Reserved</p>
            </div>
            <div class="text-center p-3 bg-yellow-50 rounded-xl">
                <p class="font-bold text-yellow-700">{{ $stats['dirty_rooms'] }}</p>
                <p class="text-yellow-600 text-xs">Dirty</p>
            </div>
        </div>
    </div>

    <!-- Revenue Overview -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 lg:col-span-2">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Revenue Overview</h3>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                <p class="text-xs font-semibold text-green-600 uppercase tracking-wider">Today</p>
                <p class="text-2xl font-extrabold text-green-700 mt-2">{{ number_format($stats['today_revenue'], 0) }}</p>
                <p class="text-xs text-green-600 mt-1">Revenue</p>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">This Week</p>
                <p class="text-2xl font-extrabold text-blue-700 mt-2">{{ number_format($stats['week_revenue'], 0) }}</p>
                <p class="text-xs text-blue-600 mt-1">Revenue</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
                <p class="text-xs font-semibold text-purple-600 uppercase tracking-wider">This Month</p>
                <p class="text-2xl font-extrabold text-purple-700 mt-2">{{ number_format($stats['month_revenue'], 0) }}</p>
                <p class="text-xs text-purple-600 mt-1">Revenue</p>
            </div>
            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-4 border border-indigo-200">
                <p class="text-xs font-semibold text-indigo-600 uppercase tracking-wider">Total</p>
                <p class="text-2xl font-extrabold text-indigo-700 mt-2">{{ number_format($stats['total_revenue'], 0) }}</p>
                <p class="text-xs text-indigo-600 mt-1">All Time</p>
            </div>
        </div>

        <!-- Today's Quick Stats -->
        <div class="mt-6 grid grid-cols-3 gap-4">
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <p class="text-2xl font-extrabold text-indigo-600">{{ $stats['today_checkins'] }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Today's Check-ins</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <p class="text-2xl font-extrabold text-red-600">{{ $stats['today_checkouts'] }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Today's Check-outs</p>
            </div>
            <div class="text-center p-4 bg-gray-50 rounded-xl">
                <p class="text-2xl font-extrabold text-yellow-600">{{ $stats['pending_reservations'] }}</p>
                <p class="text-xs text-gray-500 font-medium mt-1">Pending Reservations</p>
            </div>
        </div>
    </div>
</div>

<!-- Staff Overview & Building Stats -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Staff by Role -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Staff Overview</h3>
        <div class="space-y-3">
            @foreach($staffByRole as $roleName => $count)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-indigo-100 to-indigo-200 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <span class="font-semibold text-secondary">{{ $roleName }}</span>
                </div>
                <span class="bg-indigo-100 text-indigo-700 text-sm font-bold px-3 py-1 rounded-full">{{ $count }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Building Stats -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <h3 class="text-lg font-extrabold text-secondary mb-6">Building Overview</h3>
        <div class="space-y-3">
            @foreach($buildingStats as $building)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-semibold text-secondary">{{ $building->name }}</span>
                        <p class="text-xs text-gray-500">{{ $building->floors_count }} floors</p>
                    </div>
                </div>
                <span class="bg-blue-100 text-blue-700 text-sm font-bold px-3 py-1 rounded-full">{{ $building->rooms_count }} rooms</span>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Room Status Distribution -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-extrabold text-secondary mb-6">Room Status Distribution</h3>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @php
            $statusColors = [
                'available' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-700', 'label' => 'Available'],
                'occupied' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-700', 'label' => 'Occupied'],
                'reserved' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-700', 'label' => 'Reserved'],
                'dirty' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-700', 'label' => 'Dirty'],
                'out_of_order' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-700', 'label' => 'Out of Order'],
            ];
        @endphp
        @foreach($statusColors as $status => $colors)
        <div class="{{ $colors['bg'] }} {{ $colors['border'] }} border rounded-xl p-4 text-center">
            <p class="text-3xl font-extrabold {{ $colors['text'] }}">{{ $roomStatusCounts[$status] ?? 0 }}</p>
            <p class="text-xs font-medium {{ $colors['text'] }} mt-1">{{ $colors['label'] }}</p>
        </div>
        @endforeach
    </div>
</div>

<!-- Recent Reservations -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
    <h3 class="text-lg font-extrabold text-secondary mb-6">Recent Reservations</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                    <th class="pb-3 pr-4">Guest</th>
                    <th class="pb-3 pr-4">Room</th>
                    <th class="pb-3 pr-4">Check-in</th>
                    <th class="pb-3 pr-4">Check-out</th>
                    <th class="pb-3 pr-4">Amount</th>
                    <th class="pb-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentReservations as $reservation)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="py-3 pr-4">
                        <span class="font-medium text-secondary">{{ $reservation->guest_name ?? 'N/A' }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-sm text-gray-600">{{ $reservation->room->room_number ?? 'N/A' }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-sm text-gray-600">{{ $reservation->check_in_date ? \Carbon\Carbon::parse($reservation->check_in_date)->format('M d, Y') : 'N/A' }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="text-sm text-gray-600">{{ $reservation->check_out_date ? \Carbon\Carbon::parse($reservation->check_out_date)->format('M d, Y') : 'N/A' }}</span>
                    </td>
                    <td class="py-3 pr-4">
                        <span class="font-semibold text-secondary">{{ number_format($reservation->estimated_amount ?? 0) }}</span>
                    </td>
                    <td class="py-3">
                        @php
                            $statusBadge = match($reservation->status) {
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'confirmed' => 'bg-green-100 text-green-800',
                                'converted' => 'bg-blue-100 text-blue-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                'no_show' => 'bg-gray-100 text-gray-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $statusBadge }}">
                            {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-400">No recent reservations found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Reservation Status Distribution -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
    <h3 class="text-lg font-extrabold text-secondary mb-6">Reservation Status Overview</h3>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @php
            $resStatusColors = [
                'pending' => ['bg' => 'bg-yellow-50', 'border' => 'border-yellow-200', 'text' => 'text-yellow-700', 'label' => 'Pending'],
                'confirmed' => ['bg' => 'bg-green-50', 'border' => 'border-green-200', 'text' => 'text-green-700', 'label' => 'Confirmed'],
                'converted' => ['bg' => 'bg-blue-50', 'border' => 'border-blue-200', 'text' => 'text-blue-700', 'label' => 'Converted'],
                'cancelled' => ['bg' => 'bg-red-50', 'border' => 'border-red-200', 'text' => 'text-red-700', 'label' => 'Cancelled'],
                'no_show' => ['bg' => 'bg-gray-50', 'border' => 'border-gray-200', 'text' => 'text-gray-700', 'label' => 'No Show'],
            ];
        @endphp
        @foreach($resStatusColors as $status => $colors)
        <div class="{{ $colors['bg'] }} {{ $colors['border'] }} border rounded-xl p-4 text-center">
            <p class="text-3xl font-extrabold {{ $colors['text'] }}">{{ $reservationStatusCounts[$status] ?? 0 }}</p>
            <p class="text-xs font-medium {{ $colors['text'] }} mt-1">{{ $colors['label'] }}</p>
        </div>
        @endforeach
    </div>
</div>
@endsection
