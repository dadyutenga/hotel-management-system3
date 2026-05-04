@extends('layouts.app')

@section('title', 'Room Maintenance')
@section('page-title', 'Room Maintenance')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Room Maintenance Tracking</h2>
            <p class="text-sm text-gray-500 mt-1">View all rooms marked out of order and their maintenance progress</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-xs text-gray-500 font-medium uppercase">Total Out of Order</div>
            <div class="text-2xl font-extrabold text-red-600 mt-1">{{ $maintenanceRooms->count() }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-xs text-gray-500 font-medium uppercase">Unassigned</div>
            <div class="text-2xl font-extrabold text-yellow-600 mt-1">{{ $maintenanceRooms->whereNull('cleaning_assigned_to')->count() }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-xs text-gray-500 font-medium uppercase">In Progress</div>
            <div class="text-2xl font-extrabold text-blue-600 mt-1">{{ $maintenanceRooms->whereNotNull('cleaning_assigned_to')->whereNull('cleaning_completed_at')->count() }}</div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4">
            <div class="text-xs text-gray-500 font-medium uppercase">Awaiting Confirmation</div>
            <div class="text-2xl font-extrabold text-green-600 mt-1">{{ $maintenanceRooms->whereNotNull('cleaning_completed_at')->count() }}</div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        @if($maintenanceRooms->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gradient-to-r from-red-50 to-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase">Room</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase">Type</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase">Floor</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase">Reason</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase">Assigned To</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase">Assigned At</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase">Progress</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase">Completed At</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($maintenanceRooms as $room)
                    <tr class="hover:bg-red-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-semibold text-secondary">{{ $room->room_number }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $room->roomType->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $room->floor->name ?? '—' }}</td>
                        <td class="px-6 py-4 text-sm max-w-xs">
                            <span class="text-red-600 font-medium">{{ $room->out_of_order_reason }}</span>
                            <div class="text-xs text-gray-400 mt-0.5">by {{ $room->outOfOrderBy?->name ?? '—' }} • {{ $room->out_of_order_set_at?->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($room->cleaning_assigned_to)
                                <span class="text-blue-700 font-medium">{{ $room->cleaningAssignee->name ?? '—' }}</span>
                            @else
                                <span class="text-yellow-600 font-semibold text-xs">Not assigned</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $room->cleaning_assigned_at?->format('d M Y H:i') ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @if(!$room->cleaning_assigned_to)
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">Unassigned</span>
                            @elseif(!$room->cleaning_completed_at)
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">In Progress</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">Awaiting Confirmation</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $room->cleaning_completed_at?->format('d M Y H:i') ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="px-6 py-16 text-center">
            <div class="w-16 h-16 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-secondary">No rooms out of order</h3>
            <p class="mt-2 text-sm text-gray-500">All rooms are operational. No maintenance needed.</p>
        </div>
        @endif
    </div>
</div>
@endsection
