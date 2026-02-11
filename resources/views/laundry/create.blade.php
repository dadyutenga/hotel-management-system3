{{-- resources/views/laundry/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Laundry Task')
@section('page-title', 'Laundry Management')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Create Laundry Task</h2>
            <p class="text-sm text-gray-500 mt-1">Assign laundry service to a guest</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('laundry.store') }}" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- Reservation Selection Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Guest & Reservation
                    </h3>

                    <!-- Reservation -->
                    <div>
                        <label for="reservation_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Select Reservation <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="reservation_id" 
                            id="reservation_id"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('reservation_id') border-red-500 @enderror"
                            required>
                            <option value="">Select a reservation</option>
                            @foreach($reservations as $reservation)
                            <option value="{{ $reservation->id }}" {{ old('reservation_id') == $reservation->id ? 'selected' : '' }}>
                                {{ $reservation->guest_display_name }} - Room {{ $reservation->room->room_number }} ({{ $reservation->reservation_number }})
                            </option>
                            @endforeach
                        </select>
                        @error('reservation_id')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Assignment Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        Task Assignment
                    </h3>

                    <!-- Assign To -->
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">
                            Assign to House Help <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="assigned_to" 
                            id="assigned_to"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('assigned_to') border-red-500 @enderror"
                            required>
                            <option value="">Select house help staff</option>
                            @foreach($houseHelpStaff as $staff)
                            <option value="{{ $staff->id }}" {{ old('assigned_to') == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }} ({{ $staff->email }})
                            </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Items Description <span class="text-gray-400 text-xs">(Optional)</span>
                        </label>
                        <textarea 
                            name="description" 
                            id="description"
                            rows="3"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('description') border-red-500 @enderror"
                            placeholder="e.g., 2 shirts, 1 pants, 3 towels">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-200"></div>

                <!-- Billing Section -->
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Billing Information
                    </h3>

                    <!-- Service Type -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Service Type <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-blue-300" 
                                   :class="{ 'border-blue-500 bg-blue-50': !isAmenity, 'border-gray-300': isAmenity }"
                                   x-data="{ isAmenity: {{ old('is_amenity', '0') }} === '0' }">
                                <input type="radio" name="is_amenity" value="0" class="w-5 h-5 text-blue-600" 
                                       {{ old('is_amenity', '0') === '0' ? 'checked' : '' }}
                                       x-model="isAmenity"
                                       @change="isAmenity = ($event.target.value === '0')">
                                <div class="ml-3">
                                    <span class="block text-sm font-semibold text-gray-900">Paid Service</span>
                                    <span class="block text-xs text-gray-500">Charge to guest invoice</span>
                                </div>
                            </label>
                            
                            <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-green-300" 
                                   :class="{ 'border-green-500 bg-green-50': isAmenity, 'border-gray-300': !isAmenity }"
                                   x-data="{ isAmenity: {{ old('is_amenity', '0') }} === '1' }">
                                <input type="radio" name="is_amenity" value="1" class="w-5 h-5 text-green-600" 
                                       {{ old('is_amenity') === '1' ? 'checked' : '' }}
                                       x-model="isAmenity"
                                       @change="isAmenity = ($event.target.value === '1')">
                                <div class="ml-3">
                                    <span class="block text-sm font-semibold text-gray-900">Amenity</span>
                                    <span class="block text-xs text-gray-500">Free service</span>
                                </div>
                            </label>
                        </div>
                        @error('is_amenity')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Cost (only if not amenity) -->
                    <div id="cost-field" style="display: {{ old('is_amenity', '0') === '0' ? 'block' : 'none' }};">
                        <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">
                            Service Cost ($) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="cost" 
                            id="cost"
                            value="{{ old('cost', '0.00') }}" 
                            step="0.01"
                            min="0"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('cost') border-red-500 @enderror"
                            placeholder="0.00">
                        @error('cost')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Additional Notes <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <textarea 
                        name="notes" 
                        id="notes"
                        rows="3"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('notes') border-red-500 @enderror"
                        placeholder="Any special instructions or notes...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Info Box -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Laundry Service Guidelines:</p>
                            <ul class="list-disc list-inside space-y-1 text-blue-700">
                                <li>Amenity services are complimentary for guests</li>
                                <li>Paid services will be added to the guest's final invoice</li>
                                <li>House help will be notified of the assignment</li>
                                <li>Track progress through task status updates</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('laundry.index') }}" 
                   class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                    Cancel
                </a>
                <button 
                    type="submit"
                    class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Create Task
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amenityRadios = document.querySelectorAll('input[name="is_amenity"]');
    const costField = document.getElementById('cost-field');
    const costInput = document.getElementById('cost');

    amenityRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === '1') {
                costField.style.display = 'none';
                costInput.value = '0.00';
                costInput.removeAttribute('required');
            } else {
                costField.style.display = 'block';
                costInput.setAttribute('required', 'required');
            }
        });
    });
});
</script>
@endsection