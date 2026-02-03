{{-- resources/views/room-types/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Room Types')
@section('page-title', 'Room Types')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Room Types</h2>
            <p class="text-sm text-gray-500 mt-1">Manage room categories and pricing</p>
        </div>
        <a href="{{ route('room-types.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Room Type
        </a>
    </div>

    <!-- Room Types Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($roomTypes as $roomType)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 text-white">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="text-xl font-bold">{{ $roomType->name }}</h3>
                    <span class="text-xs font-semibold bg-white/20 px-2 py-1 rounded">{{ $roomType->code }}</span>
                </div>
                <div class="text-3xl font-bold">${{ number_format($roomType->base_rate, 2) }}</div>
                <div class="text-sm text-blue-100">per night</div>
            </div>

            <!-- Body -->
            <div class="p-6">
                @if($roomType->description)
                <p class="text-sm text-gray-600 mb-4 line-clamp-3">{{ $roomType->description }}</p>
                @endif

                <!-- Stats -->
                <div class="space-y-3 mb-6">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Max Occupancy
                        </span>
                        <span class="font-semibold text-gray-900">{{ $roomType->max_occupancy }} guests</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Total Rooms
                        </span>
                        <span class="font-semibold text-gray-900">{{ $roomType->rooms->count() }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('room-types.edit', $roomType) }}" 
                       class="flex-1 text-center px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('room-types.destroy', $roomType) }}" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                onclick="return confirm('Are you sure you want to delete this room type?')"
                                class="w-full px-4 py-2 text-sm font-medium text-red-700 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No room types</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new room type.</p>
                <div class="mt-6">
                    <a href="{{ route('room-types.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add Room Type
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($roomTypes->hasPages())
    <div class="mt-6">
        {{ $roomTypes->links() }}
    </div>
    @endif
</div>
@endsection