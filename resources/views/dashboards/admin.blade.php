{{-- resources/views/dashboards/admin.blade.php --}}
@extends('layouts.app')

@section('title', 'Admin Dashboard')
@section('page-title', 'Admin Dashboard')

@section('content')
<!-- Top Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm">Total Buildings</p>
                <p class="text-3xl font-bold">{{ $stats['total_buildings'] }}</p>
            </div>
            <div class="text-5xl opacity-30">🏢</div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm">Total Rooms</p>
                <p class="text-3xl font-bold">{{ $stats['total_rooms'] }}</p>
                <p class="text-xs text-green-100">{{ $stats['active_rooms'] }} active</p>
            </div>
            <div class="text-5xl opacity-30">🛏️</div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm">Total Users</p>
                <p class="text-3xl font-bold">{{ $stats['total_users'] }}</p>
            </div>
            <div class="text-5xl opacity-30">👥</div>
        </div>
    </div>

    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-orange-100 text-sm">Total Reservations</p>
                <p class="text-3xl font-bold">{{ $stats['total_reservations'] }}</p>
                <p class="text-xs text-orange-100">{{ $stats['pending_reservations'] }} pending</p>
            </div>
            <div class="text-5xl opacity-30">📅</div>
        </div>
    </div>
</div>

<!-- Room & Revenue Stats -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Room Status Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Room Status Overview</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <div class="text-3xl font-bold text-green-600">{{ $stats['available_rooms'] }}</div>
                <div class="text-sm text-gray-600">Available</div>
            </div>
            <div class="text-center p-4 bg-red-50 rounded-lg">
                <div class="text-3xl font-bold text-red-600">{{ $stats['occupied_rooms'] }}</div>
                <div class="text-sm text-gray-600">Occupied</div>
            </div>
            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                <div class="text-3xl font-bold text-yellow-600">{{ $stats['reserved_rooms'] }}</div>
                <div class="text-sm text-gray-600">Reserved</div>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <div class="text-3xl font-bold text-blue-600">{{ $roomStatusCounts['dirty'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">Needs Cleaning</div>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Occupancy Rate</span>
                <span class="font-semibold">{{ $stats['total_rooms'] > 0 ? round(($stats['occupied_rooms'] / $stats['total_rooms']) * 100, 1) : 0 }}%</span>
            </div>
        </div>
    </div>

    <!-- Revenue Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Revenue Overview</h3>
        <div class="space-y-4">
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <div>
                    <p class="text-sm text-gray-600">Today</p>
                    <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['today_revenue'], 2) }}</p>
                </div>
                <div class="text-3xl">💵</div>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <div>
                    <p class="text-sm text-gray-600">This Week</p>
                    <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['week_revenue'], 2) }}</p>
                </div>
                <div class="text-3xl">📊</div>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <div>
                    <p class="text-sm text-gray-600">This Month</p>
                    <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['month_revenue'], 2) }}</p>
                </div>
                <div class="text-3xl">💰</div>
            </div>
        </div>
    </div>
</div>

<!-- Today's Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Today's Check-ins/Check-outs</h3>
        <div class="grid grid-cols-2 gap-4">
            <div class="text-center p-6 bg-green-50 rounded-lg border-2 border-green-200">
                <div class="text-4xl font-bold text-green-600">{{ $stats['today_checkins'] }}</div>
                <div class="text-sm text-gray-600 mt-2">Expected Check-ins</div>
            </div>
            <div class="text-center p-6 bg-red-50 rounded-lg border-2 border-red-200">
                <div class="text-4xl font-bold text-red-600">{{ $stats['today_checkouts'] }}</div>
                <div class="text-sm text-gray-600 mt-2">Expected Check-outs</div>
            </div>
        </div>
    </div>

    <!-- Reservation Status -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Reservation Status Distribution</h3>
        <div class="space-y-3">
            @foreach(['pending' => 'yellow', 'confirmed' => 'green', 'checked_in' => 'blue', 'checked_out' => 'gray', 'cancelled' => 'red'] as $status => $color)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full bg-{{ $color }}-500 mr-2"></div>
                        <span class="text-sm capitalize">{{ str_replace('_', ' ', $status) }}</span>
                    </div>
                    <span class="font-semibold">{{ $reservationStatusCounts[$status] ?? 0 }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Building Stats -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h3 class="text-lg font-semibold mb-4">Building Overview</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($buildingStats as $building)
        <div class="border rounded-lg p-4 hover:shadow-md transition">
            <h4 class="font-semibold text-gray-800">{{ $building->name }}</h4>
            <p class="text-sm text-gray-600">Code: {{ $building->code }}</p>
            <div class="mt-3 pt-3 border-t grid grid-cols-2 gap-2 text-sm">
                <div>
                    <span class="text-gray-600">Floors:</span>
                    <span class="font-semibold ml-1">{{ $building->floors_count }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Rooms:</span>
                    <span class="font-semibold ml-1">{{ $building->floors_sum_rooms_count ?? 0 }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Reservations -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Recent Reservations</h3>
        <div class="space-y-3">
            @forelse($recentReservations as $reservation)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded hover:bg-gray-100 transition">
                <div class="flex-1">
                    <p class="font-medium text-gray-800">{{ $reservation->guest_name }}</p>
                    <p class="text-xs text-gray-500">{{ $reservation->reservation_number }}</p>
                </div>
                <div class="text-right">
                    @include('components.reservation-status-badge', ['status' => $reservation->status])
                    <p class="text-xs text-gray-500 mt-1">{{ $reservation->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">No recent reservations</p>
            @endforelse
        </div>
        <a href="{{ route('reservations.index') }}" class="block text-center text-blue-600 hover:text-blue-800 mt-4 text-sm font-medium">
            View All Reservations →
        </a>
    </div>

    <!-- Recent Users -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold mb-4">Recent Users</h3>
        <div class="space-y-3">
            @forelse($recentUsers as $user)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded hover:bg-gray-100 transition">
                <div class="flex-1">
                    <p class="font-medium text-gray-800">{{ $user->name }}</p>
                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs rounded bg-purple-100 text-purple-800">
                        {{ $user->role->display_name }}
                    </span>
                    <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <p class="text-gray-500 text-center py-4">No recent users</p>
            @endforelse
        </div>
        <a href="{{ route('users.index') }}" class="block text-center text-blue-600 hover:text-blue-800 mt-4 text-sm font-medium">
            View All Users →
        </a>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-6 bg-white rounded-lg shadow p-6">
    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('buildings.create') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition">
            <span class="text-3xl mb-2">🏢</span>
            <span class="text-sm font-medium text-gray-700">Add Building</span>
        </a>
        <a href="{{ route('rooms.create') }}" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
            <span class="text-3xl mb-2">🛏️</span>
            <span class="text-sm font-medium text-gray-700">Add Room</span>
        </a>
        <a href="{{ route('users.create') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition">
            <span class="text-3xl mb-2">👤</span>
            <span class="text-sm font-medium text-gray-700">Add User</span>
        </a>
        <a href="{{ route('reservations.create') }}" class="flex flex-col items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition">
            <span class="text-3xl mb-2">📅</span>
            <span class="text-sm font-medium text-gray-700">New Reservation</span>
        </a>
    </div>
</div>
@endsection