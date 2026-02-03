{{-- resources/views/dashboards/supervisor.blade.php --}}
@extends('layouts.app')

@section('title', 'Supervisor Dashboard')
@section('page-title', 'Supervisor Dashboard')

@section('content')
<!-- Top Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm">Available Rooms</p>
                <p class="text-3xl font-bold">{{ $stats['available_rooms'] }}</p>
            </div>
            <div class="text-5xl opacity-30">✅</div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-sm">Occupied Rooms</p>
                <p class="text-3xl font-bold">{{ $stats['occupied_rooms'] }}</p>
            </div>
            <div class="text-5xl opacity-30">🔴</div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-sm">Needs Cleaning</p>
                <p class="text-3xl font-bold">{{ $stats['dirty_rooms'] }}</p>
            </div>
            <div class="text-5xl opacity-30">🧹</div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-gray-600 to-gray-700 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-100 text-sm">Out of Order</p>
                <p class="text-3xl font-bold">{{ $stats['out_of_order_rooms'] }}</p>
            </div>
            <div class="text-5xl opacity-30">⚠️</div>
        </div>
    </div>
</div>

<!-- Occupancy & Today's Activity -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Occupancy Rate</h3>
        <div class="flex items-center justify-center">
            <div class="relative w-40 h-40">
                <svg class="transform -rotate-90 w-40 h-40">
                    <circle cx="80" cy="80" r="70" stroke="#e5e7eb" stroke-width="10" fill="none" />
                    <circle cx="80" cy="80" r="70" stroke="#3b82f6" stroke-width="10" fill="none"
                            stroke-dasharray="{{ 2 * 3.14159 * 70 }}"
                            stroke-dashoffset="{{ 2 * 3.14159 * 70 * (1 - $stats['occupancy_rate'] / 100) }}"
                            stroke-linecap="round" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-3xl font-bold text-gray-800">{{ $stats['occupancy_rate'] }}%</span>
                </div>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-2 text-sm">
            <div class="text-center">
                <div class="font-semibold text-gray-800">{{ $stats['occupied_rooms'] }}</div>
                <div class="text-gray-500">Occupied</div>
            </div>
            <div class="text-center">
                <div class="font-semibold text-gray-800">{{ $stats['total_rooms'] }}</div>
                <div class="text-gray-500">Total</div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Today's Activity</h3>
        <div class="grid grid-cols-3 gap-4">
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-3xl font-bold text-green-600">{{ $stats['today_checkins'] }}</div>
                <div class="text-sm text-gray-600">Check-ins</div>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <div class="text-3xl font-bold text-red-600">{{ $stats['today_checkouts'] }}</div>
                <div class="text-sm text-gray-600">Check-outs</div>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <div class="text-3xl font-bold text-yellow-600">{{ $stats['pending_reservations'] }}</div>
                <div class="text-sm text-gray-600">Pending</div>
            </div>
        </div>

        <div class="mt-6 pt-6 border-t">
            <h4 class="font-semibold mb-3">Revenue Today</h4>
            <div class="grid grid-cols-3 gap-4 text-sm">
                <div>
                    <p class="text-gray-600">Today</p>
                    <p class="text-xl font-bold text-gray-800">${{ number_format($stats['today_revenue'], 2) }}</p>
                </div>
                <div>
                    <p class="text-gray-600">This Week</p>
                    <p class="text-xl font-bold text-gray-800">${{ number_format($stats['week_revenue'], 2) }}</p>
                </div>
                <div>
                    <p class="text-gray-600">This Month</p>
                    <p class="text-xl font-bold text-gray-800">${{ number_format($stats['month_revenue'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Room Status Distribution -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Room Status Distribution</h3>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="text-center p-4 bg-green-50 rounded-lg border-2 border-green-200">
            <div class="text-4xl mb-2">✅</div>
            <div class="text-2xl font-bold text-green-600">{{ $roomStatusCounts['available'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Available</div>
        </div>
        <div class="text-center p-4 bg-yellow-50 rounded-lg border-2 border-yellow-200">
            <div class="text-4xl mb-2">📌</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $roomStatusCounts['reserved'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Reserved</div>
        </div>
        <div class="text-center p-4 bg-red-50 rounded-lg border-2 border-red-200">
            <div class="text-4xl mb-2">🔴</div>
            <div class="text-2xl font-bold text-red-600">{{ $roomStatusCounts['occupied'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Occupied</div>
        </div>
        <div class="text-center p-4 bg-gray-50 rounded-lg border-2 border-gray-200">
            <div class="text-4xl mb-2">🧹</div>
            <div class="text-2xl font-bold text-gray-600">{{ $roomStatusCounts['dirty'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Dirty</div>
        </div>
        <div class="text-center p-4 bg-gray-100 rounded-lg border-2 border-gray-300">
            <div class="text-4xl mb-2">⚠️</div>
            <div class="text-2xl font-bold text-gray-800">{{ $roomStatusCounts['out_of_order'] ?? 0 }}</div>
            <div class="text-sm text-gray-600">Out of Order</div>
        </div>
    </div>
</div>

<!-- Rooms Needing Attention -->
@if($roomsNeedingAttention->count() > 0)
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4 text-red-600">⚠️ Rooms Requiring Attention</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Floor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($roomsNeedingAttention as $room)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap font-medium">{{ $room->room_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $room->floor->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $room->roomType->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @include('components.room-status-badge', ['status' => $room->status])
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('rooms.edit', $room) }}" class="text-blue-600 hover:text-blue-900">Update Status</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Today's Check-ins/Check-outs -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Today's Check-ins & Check-outs</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reservation #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guest</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Activity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($todayActivity as $reservation)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->reservation_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->guest_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($reservation->check_in_date->isToday())
                            <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">Check-In</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">Check-Out</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->room?->room_number ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @include('components.reservation-status-badge', ['status' => $reservation->status])
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($reservation->status === 'confirmed' && $reservation->check_in_date->isToday())
                            <form method="POST" action="{{ route('reservations.check-in', $reservation) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-900">Check In</button>
                            </form>
                        @elseif($reservation->status === 'checked_in' && $reservation->check_out_date->isToday())
                            <form method="POST" action="{{ route('reservations.check-out', $reservation) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-900">Check Out</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">No activity today</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Upcoming Arrivals -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Upcoming Arrivals (Next 7 Days)</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reservation #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guest</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check-In</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($upcomingArrivals as $reservation)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->reservation_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->guest_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->check_in_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->room?->room_number ?? 'Not Assigned' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @include('components.reservation-status-badge', ['status' => $reservation->status])
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No upcoming arrivals</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection