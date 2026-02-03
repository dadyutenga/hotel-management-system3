{{-- resources/views/dashboards/front-desk.blade.php --}}
@extends('layouts.app')

@section('title', 'Front Desk Dashboard')
@section('page-title', 'Front Desk Dashboard')

@section('content')
<!-- Top Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm">Available Rooms</p>
                <p class="text-4xl font-bold">{{ $stats['available_rooms'] }}</p>
                <p class="text-xs text-green-100 mt-1">Ready for booking</p>
            </div>
            <div class="text-6xl opacity-30">✅</div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm">Today's Check-ins</p>
                <p class="text-4xl font-bold">{{ $stats['today_checkins'] }}</p>
                <p class="text-xs text-blue-100 mt-1">Expected arrivals</p>
            </div>
            <div class="text-6xl opacity-30">➡️</div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-sm">Today's Check-outs</p>
                <p class="text-4xl font-bold">{{ $stats['today_checkouts'] }}</p>
                <p class="text-xs text-red-100 mt-1">Expected departures</p>
            </div>
            <div class="text-6xl opacity-30">⬅️</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('reservations.create') }}" class="flex flex-col items-center p-6 bg-blue-50 rounded-lg hover:bg-blue-100 transition border-2 border-blue-200">
            <span class="text-5xl mb-3">📅</span>
            <span class="text-sm font-medium text-gray-700">New Reservation</span>
        </a>
        <a href="{{ route('reservations.index') }}" class="flex flex-col items-center p-6 bg-green-50 rounded-lg hover:bg-green-100 transition border-2 border-green-200">
            <span class="text-5xl mb-3">📋</span>
            <span class="text-sm font-medium text-gray-700">View Reservations</span>
        </a>
        <div class="flex flex-col items-center p-6 bg-yellow-50 rounded-lg border-2 border-yellow-200">
            <span class="text-5xl mb-3">⏳</span>
            <span class="text-sm font-medium text-gray-700">{{ $stats['pending_reservations'] }} Pending</span>
        </div>
        <div class="flex flex-col items-center p-6 bg-purple-50 rounded-lg border-2 border-purple-200">
            <span class="text-5xl mb-3">🛏️</span>
            <span class="text-sm font-medium text-gray-700">{{ $stats['occupied_rooms'] }} Occupied</span>
        </div>
    </div>
</div>

<!-- Available Rooms by Type -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Available Rooms by Type</h3>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
        @forelse($availableRoomsByType as $item)
        <div class="text-center p-4 bg-green-50 rounded-lg border border-green-200">
            <div class="text-3xl font-bold text-green-600">{{ $item->count }}</div>
            <div class="text-sm text-gray-600 mt-1">{{ $item->roomType->name }}</div>
            <div class="text-xs text-gray-500 mt-1">${{ number_format($item->roomType->base_rate, 0) }}/night</div>
        </div>
        @empty
        <div class="col-span-full text-center text-gray-500 py-4">No available rooms</div>
        @endforelse
    </div>
</div>

<!-- Today's Activity -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Today's Check-ins & Check-outs</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reservation #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guest Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Activity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($todayActivity as $reservation)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $reservation->reservation_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->guest_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->guest_phone }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($reservation->check_in_date->isToday())
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Check-In</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Check-Out</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->room?->room_number ?? 'Not Assigned' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @include('components.reservation-status-badge', ['status' => $reservation->status])
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('reservations.edit', $reservation) }}" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
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
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No activity scheduled for today</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Upcoming Arrivals -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Upcoming Arrivals (Next 3 Days)</h3>
    <div class="overflow-x-auto">
        <table class="min-w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reservation #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guest</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Phone</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Room</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Guests</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($upcomingArrivals as $reservation)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        {{ $reservation->check_in_date->format('M d') }}
                        <span class="text-xs text-gray-500 block">{{ $reservation->check_in_date->diffForHumans() }}</span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->reservation_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $reservation->guest_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->guest_phone }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($reservation->room)
                            <span class="font-medium">{{ $reservation->room->room_number }}</span>
                        @else
                            <span class="text-red-600">Not Assigned</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $reservation->number_of_guests }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @include('components.reservation-status-badge', ['status' => $reservation->status])
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No upcoming arrivals</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- My Recent Reservations -->
<div class="bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">My Recent Reservations</h3>
    <div class="space-y-3">
        @forelse($myRecentReservations as $reservation)
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
            <div class="flex-1">
                <p class="font-medium text-gray-800">{{ $reservation->guest_name }}</p>
                <p class="text-sm text-gray-600">{{ $reservation->reservation_number }}</p>
                <p class="text-xs text-gray-500">{{ $reservation->check_in_date->format('M d') }} - {{ $reservation->check_out_date->format('M d, Y') }}</p>
            </div>
            <div class="text-right ml-4">
                @include('components.reservation-status-badge', ['status' => $reservation->status])
                <p class="text-xs text-gray-500 mt-1">{{ $reservation->created_at->diffForHumans() }}</p>
            </div>
            <div class="ml-4">
                <a href="{{ route('reservations.edit', $reservation) }}" class="text-blue-600 hover:text-blue-900 text-sm">View →</a>
            </div>
        </div>
        @empty
        <p class="text-gray-500 text-center py-8">No recent reservations</p>
        @endforelse
    </div>
</div>
@endsection