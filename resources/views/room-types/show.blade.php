{{-- resources/views/room-types/show.blade.php --}}
@extends('layouts.app')

@section('title', $roomType->name)
@section('page-title', 'Room Types')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Header Image -->
        <div class="relative h-64 overflow-hidden bg-gray-100">
            <img src="{{ $roomType->medium_image_with_fallback }}"
                 alt="{{ $roomType->name }}"
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
            <div class="absolute bottom-0 left-0 p-6 text-white">
                <div class="flex items-center gap-3 mb-2">
                    <h2 class="text-3xl font-extrabold">{{ $roomType->name }}</h2>
                    <span class="text-sm font-bold bg-white/20 px-2.5 py-1 rounded-lg">{{ $roomType->code }}</span>
                </div>
                <p class="text-xl font-semibold">{{ $roomType->formatted_rate }} <span class="text-sm font-normal text-gray-200">/ night</span></p>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-4 border border-blue-100">
                    <p class="text-sm text-gray-500">Max Occupancy</p>
                    <p class="text-2xl font-bold text-secondary">{{ $roomType->max_occupancy }} guests</p>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-4 border border-blue-100">
                    <p class="text-sm text-gray-500">Total Rooms</p>
                    <p class="text-2xl font-bold text-secondary">{{ $roomType->rooms->count() }}</p>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl p-4 border border-blue-100">
                    <p class="text-sm text-gray-500">Currency</p>
                    <p class="text-2xl font-bold text-secondary">{{ $roomType->currency }}</p>
                </div>
            </div>

            <!-- Description -->
            @if($roomType->description)
            <div>
                <h3 class="text-lg font-bold text-secondary mb-2">Description</h3>
                <p class="text-gray-600 leading-relaxed">{{ $roomType->description }}</p>
            </div>
            @endif

            <!-- Gallery -->
            @if($roomType->hasGalleryImages())
            <div>
                <h3 class="text-lg font-bold text-secondary mb-4">Gallery ({{ $roomType->gallery_images_count }})</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    @foreach($roomType->gallery_images as $image)
                    <a href="{{ $image->getUrl() }}" target="_blank" class="relative group aspect-square overflow-hidden rounded-xl border border-gray-200">
                        <img src="{{ $image->getUrl('thumb') ?: $image->getUrl() }}"
                             alt="{{ $image->file_name }}"
                             class="w-full h-full object-cover transition-transform group-hover:scale-105">
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                <a href="{{ route('room-types.index') }}" class="px-6 py-2.5 text-sm font-semibold text-secondary bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all">
                    Back to Room Types
                </a>
                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('room-types.destroy', $roomType) }}" onsubmit="return confirm('Are you sure you want to delete this room type?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-red-700 bg-red-50 border border-red-200 rounded-xl hover:bg-red-100 transition-all">
                            Delete
                        </button>
                    </form>
                    <a href="{{ route('room-types.edit', $roomType) }}" class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg transition-all">
                        Edit Room Type
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
