{{-- resources/views/bookings/current-guests.blade.php --}}
@extends('layouts.app')

@section('title', __('bookings.current_guests'))
@section('page-title', __('bookings.current_guests'))

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ __('bookings.current_guests') }}</h2>
            <p class="text-sm text-gray-500 mt-1">Guests currently staying at the hotel</p>
        </div>
        <a href="{{ route('bookings.create') }}" 
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('bookings.new_booking') }}
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $totalGuests }}</div>
                    <div class="text-xs text-gray-500 font-medium">Total Checked In</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $totalRooms }}</div>
                    <div class="text-xs text-gray-500 font-medium">Rooms Occupied</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $todayCheckins }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('bookings.todays_checkins') }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 hover:shadow-xl transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-extrabold text-secondary">{{ $todayCheckouts }}</div>
                    <div class="text-xs text-gray-500 font-medium">{{ __('bookings.todays_checkouts') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <form method="GET" action="{{ route('bookings.current-guests') }}" class="flex gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search guest name, email, room number, booking #..."
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all">
            </div>
            <button type="submit" class="px-4 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                {{ __('bookings.filter') }}
            </button>
            <a href="{{ route('bookings.current-guests') }}" class="px-4 py-2.5 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-all">
                {{ __('bookings.reset') }}
            </a>
        </form>
    </div>

    <!-- Current Guests List -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gradient-to-r from-blue-50 to-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.table.booking') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.table.guest') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.table.room') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.table.check_in') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.table.check_out') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.table.amount') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">{{ __('bookings.table.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($currentGuests as $booking)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center text-white text-xs font-bold shadow-lg">
                                    {{ substr($booking->booking_number, -4) }}
                                </div>
                                <div class="ml-3">
                                    <a href="{{ route('bookings.show', $booking) }}" class="text-sm font-semibold text-secondary hover:text-primary transition-colors">
                                        {{ $booking->booking_number }}
                                    </a>
                                    <div class="text-xs text-gray-500">{{ $booking->created_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($booking->guest)
                                <a href="{{ route('guests.show', $booking->guest) }}" class="group">
                                    <div class="text-sm font-semibold text-secondary group-hover:text-primary transition-colors">{{ $booking->guest_display_name }}</div>
                                    <div class="text-xs text-gray-500">{{ $booking->guest_display_phone }}</div>
                                </a>
                            @else
                                <div class="text-sm font-semibold text-secondary">{{ $booking->guest_name }}</div>
                                <div class="text-xs text-gray-500">{{ $booking->guest_phone }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($booking->room)
                                <div class="text-sm font-medium text-secondary">{{ $booking->room->room_number }}</div>
                                <div class="text-xs text-primary">{{ $booking->room->roomType->name ?? '' }}</div>
                            @else
                                <span class="text-xs text-red-600 font-semibold">{{ __('bookings.table.not_assigned') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-secondary">{{ $booking->check_in_date->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $booking->check_in_date->format('D') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-secondary">{{ $booking->check_out_date->format('M d, Y') }}</div>
                            <div class="text-xs text-primary">{{ $booking->nights }} {{ __('bookings.table.nights') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-bold text-secondary">@currency($booking->total_amount)</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('bookings.show', $booking) }}" class="text-primary hover:text-blue-700 font-semibold" title="View">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>

                                @if($booking->canBeEdited())
                                    <a href="{{ route('bookings.edit', $booking) }}" class="text-primary hover:text-blue-700 font-semibold" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                @endif

                                @if($booking->canBeCheckedOut())
                                    <form method="POST" action="{{ route('bookings.check-out', $booking) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-gray-600 hover:text-gray-700 font-semibold text-xs" title="Check Out">
                                            Check Out
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="w-16 h-16 bg-gradient-to-br from-primary/10 to-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-secondary">No current guests</h3>
                            <p class="mt-2 text-sm text-gray-500">There are no guests currently checked in at the hotel.</p>
                            <div class="mt-6">
                                <a href="{{ route('bookings.create') }}" 
                                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    New Booking
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($currentGuests->hasPages())
    <div class="mt-6">
        {{ $currentGuests->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
