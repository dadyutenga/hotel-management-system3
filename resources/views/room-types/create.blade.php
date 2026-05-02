{{-- resources/views/room-types/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Create Room Type')
@section('page-title', 'Room Types')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-2xl">
            <h2 class="text-xl font-extrabold text-secondary">Create Room Type</h2>
            <p class="text-sm text-gray-500 mt-1">Define a new room category</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('room-types.store') }}" enctype="multipart/form-data" class="p-6">
            @csrf

            <div class="space-y-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-secondary mb-2">
                        Name <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name"
                        value="{{ old('name') }}" 
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('name') border-red-500 @enderror"
                        placeholder="e.g., Deluxe Suite, Standard Room"
                        required>
                    @error('name')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Code -->
                <div>
                    <label for="code" class="block text-sm font-semibold text-secondary mb-2">
                        Code <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="code" 
                        id="code"
                        value="{{ old('code') }}" 
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('code') border-red-500 @enderror"
                        placeholder="e.g., DLX, STD"
                        required>
                    @error('code')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <!-- Base Rate -->
                    <div>
                        <label for="base_rate" class="block text-sm font-semibold text-secondary mb-2">
                            Base Rate ({{ CurrencyHelper::getCurrencySymbol() }}) <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="base_rate" 
                            id="base_rate"
                            value="{{ old('base_rate', 0) }}" 
                            step="{{ CurrencyHelper::getDecimals() === 0 ? '1' : '0.01' }}"
                            min="0"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('base_rate') border-red-500 @enderror"
                            placeholder="0.{{ CurrencyHelper::getDecimals() === 0 ? '' : '00' }}"
                            required>
                        <input type="hidden" name="currency" value="{{ CurrencyHelper::getDefaultCurrency() }}">
                        @error('base_rate')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Max Occupancy -->
                    <div>
                        <label for="max_occupancy" class="block text-sm font-semibold text-secondary mb-2">
                            Max Occupancy <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="max_occupancy" 
                            id="max_occupancy"
                            value="{{ old('max_occupancy', 2) }}" 
                            min="1"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('max_occupancy') border-red-500 @enderror"
                            placeholder="2"
                            required>
                        @error('max_occupancy')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-secondary mb-2">
                        Description
                    </label>
                    <textarea 
                        name="description" 
                        id="description"
                        rows="4"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all resize-none @error('description') border-red-500 @enderror"
                        placeholder="Enter room type description, amenities, etc.">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="border-t border-gray-100"></div>

                <!-- Images Section -->
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        Room Type Images
                    </h3>

                    <!-- Main Image -->
                    <div class="mb-6">
                        <label for="image" class="block text-sm font-semibold text-secondary mb-2">
                            Main Image <span class="text-gray-400 text-xs font-normal">(Optional, max 2MB, JPG/PNG/WebP)</span>
                        </label>
                        <input 
                            type="file" 
                            name="image" 
                            id="image"
                            accept=".jpg,.jpeg,.png,.webp"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 @error('image') border-red-500 @enderror">
                        @error('image')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Gallery Images -->
                    <div>
                        <label for="gallery" class="block text-sm font-semibold text-secondary mb-2">
                            Gallery Images <span class="text-gray-400 text-xs font-normal">(Optional, max 2MB each, JPG/PNG/WebP, multiple allowed)</span>
                        </label>
                        <input 
                            type="file" 
                            name="gallery[]" 
                            id="gallery"
                            accept=".jpg,.jpeg,.png,.webp"
                            multiple
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 @error('gallery.*') border-red-500 @enderror">
                        @error('gallery.*')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('room-types.index') }}" 
                   class="px-6 py-2.5 text-sm font-semibold text-secondary bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                    Cancel
                </a>
                <button 
                    type="submit"
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                    Create Room Type
                </button>
            </div>
        </form>
    </div>
</div>
@endsection