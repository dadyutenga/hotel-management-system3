{{-- resources/views/laundry/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Laundry Task')
@section('page-title', 'Laundry Management')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Edit Laundry Task</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ $laundryTask->task_number }}</p>
                </div>
                @include('components.laundry-status-badge', ['status' => $laundryTask->status])
            </div>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('laundry.update', $laundryTask) }}" class="p-6">
            @csrf
            @method('PUT')

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
                            <option value="{{ $reservation->id }}" {{ old('reservation_id', $laundryTask->reservation_id) == $reservation->id ? 'selected' : '' }}>
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
                            <option value="{{ $staff->id }}" {{ old('assigned_to', $laundryTask->assigned_to) == $staff->id ? 'selected' : '' }}>
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
                            placeholder="e.g., 2 shirts, 1 pants, 3 towels">{{ old('description', $laundryTask->description) }}</textarea>
                        @error('description')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="mt-6">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Task Status <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="status" 
                            id="status"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('status') border-red-500 @enderror"
                            required>
                            <option value="pending" {{ old('status', $laundryTask->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ old('status', $laundryTask->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status', $laundryTask->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="returned" {{ old('status', $laundryTask->status) == 'returned' ? 'selected' : '' }}>Returned</option>
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
                                   :class="{ 'border-blue-500 bg-blue-50': !isAmenity, 'border-gray-300': isAmenity }">
                                <input type="radio" name="is_amenity" value="0" class="w-5 h-5 text-blue-600" 
                                       {{ old('is_amenity', $laundryTask->is_amenity ? '1' : '0') === '0' ? 'checked' : '' }}>
                                <div class="ml-3">
                                    <span class="block text-sm font-semibold text-gray-900">Paid Service</span>
                                    <span class="block text-xs text-gray-500">Charge to guest invoice</span>
                                </div>
                            </label>
                            
                            <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-green-300" 
                                   :class="{ 'border-green-500 bg-green-50': isAmenity, 'border-gray-300': !isAmenity }">
                                <input type="radio" name="is_amenity" value="1" class="w-5 h-5 text-green-600" 
                                       {{ old('is_amenity', $laundryTask->is_amenity ? '1' : '0') === '1' ? 'checked' : '' }}>
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
                    <div id="cost-field" style="display: {{ old('is_amenity', $laundryTask->is_amenity ? '1' : '0') === '0' ? 'block' : 'none' }};">
                        <label for="cost" class="block text-sm font-medium text-gray-700 mb-2">
                            Service Cost ($) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="cost" 
                            id="cost"
                            value="{{ old('cost', $laundryTask->cost) }}" 
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
                        placeholder="Any special instructions or notes...">{{ old('notes', $laundryTask->notes) }}</textarea>
                    @error('notes')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Task Info -->
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Task Information</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Created By:</span>
                            <span class="text-gray-900 font-medium ml-2">{{ $laundryTask->creator->name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Created:</span>
                            <span class="text-gray-900 font-medium ml-2">{{ $laundryTask->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        @if($laundryTask->collected_at)
                        <div>
                            <span class="text-gray-500">Collected:</span>
                            <span class="text-gray-900 font-medium ml-2">{{ $laundryTask->collected_at->format('M d, Y H:i') }}</span>
                        </div>
                        @endif
                        @if($laundryTask->completed_at)
                        <div>
                            <span class="text-gray-500">Completed:</span>
                            <span class="text-gray-900 font-medium ml-2">{{ $laundryTask->completed_at->format('M d, Y H:i') }}</span>
                        </div>
                        @endif
                        @if($laundryTask->returned_at)
                        <div>
                            <span class="text-gray-500">Returned:</span>
                            <span class="text-gray-900 font-medium ml-2">{{ $laundryTask->returned_at->format('M d, Y H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
                <button 
                    type="button"
                    onclick="if(confirm('Are you sure you want to delete this task?')) { document.getElementById('delete-form').submit(); }"
                    class="px-6 py-2.5 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                    Delete Task
                </button>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('laundry.index') }}" 
                       class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        Cancel
                    </a>
                    <button 
                        type="submit"
                        class="px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Update Task
                    </button>
                </div>
            </div>
        </form>

        <!-- Hidden Delete Form -->
        <form id="delete-form" method="POST" action="{{ route('laundry.destroy', $laundryTask) }}" class="hidden">
            @csrf
            @method('DELETE')
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