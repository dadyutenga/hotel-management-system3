{{-- resources/views/rooms/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Room')
@section('page-title', 'Rooms')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Create Room</h2>
            <p class="text-sm text-gray-500 mt-1">Add a new room to your inventory</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('rooms.store') }}" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- Floor -->
                <div>
                    <label for="floor_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Floor <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="floor_id" 
                        id="floor_id"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('floor_id') border-red-500 @enderror"
                        required>
                        <option value="">Select a floor</option>
                        @foreach($floors as $floor)
                        <option value="{{ $floor->id }}" {{ old('floor_id') == $floor->id ? 'selected' : '' }}>
                            {{ $floor->building->name }} - {{ $floor->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('floor_id')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Room Type -->
                <div>
                    <label for="room_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Room Type <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="room_type_id" 
                        id="room_type_id"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('room_type_id') border-red-500 @enderror"
                        required>
                        <option value="">Select a room type</option>
                        @foreach($roomTypes as $roomType)
                        <option value="{{ $roomType->id }}" {{ old('room_type_id') == $roomType->id ? 'selected' : '' }}>
                            {{ $roomType->name }} ({{ $roomType->code }}) - ${{ number_format($roomType->base_rate, 2) }}
                        </option>
                        @endforeach
                    </select>
                    @error('room_type_id')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Room Number -->
                <div>
                    <label for="room_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Room Number <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="room_number" 
                        id="room_number"
                        value="{{ old('room_number') }}" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('room_number') border-red-500 @enderror"
                        placeholder="e.g., 101, 102, A-201"
                        required>
                    @error('room_number')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select 
                        name="status" 
                        id="status"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('status') border-red-500 @enderror"
                        required>
                        <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                        <option value="reserved" {{ old('status') == 'reserved' ? 'selected' : '' }}>Reserved</option>
                        <option value="dirty" {{ old('status') == 'dirty' ? 'selected' : '' }}>Needs Cleaning</option>
                        <option value="out_of_order" {{ old('status') == 'out_of_order' ? 'selected' : '' }}>Out of Order</option>
                    </select>
                    @error('status')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Active Status -->
                <div class="flex items-center gap-3">
                    <input 
                        type="checkbox" 
                        name="is_active" 
                        id="is_active"
                        value="1" 
                        {{ old('is_active', true) ? 'checked' : '' }}
                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                    <label for="is_active" class="text-sm font-medium text-gray-700 cursor-pointer">
                        Active
                    </label>
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Room Numbering Tips:</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-700">
                                <li>Use floor number as prefix (e.g., 101, 201)</li>
                                <li>Keep numbering consistent across floors</li>
                                <li>Consider building codes for multi-building properties</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('rooms.index') }}" 
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                    Cancel
                </a>
                <button 
                    type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Create Room
                </button>
            </div>
        </form>
    </div>
</div>
@endsection