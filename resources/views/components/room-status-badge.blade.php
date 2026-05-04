{{-- resources/views/components/room-status-badge.blade.php --}}
@php
    $classes = match($status) {
        'available' => 'bg-green-100 text-green-800',
        'reserved' => 'bg-yellow-100 text-yellow-800',
        'occupied' => 'bg-red-100 text-red-800',
        'dirty' => 'bg-gray-100 text-gray-800',
        'out_of_order' => 'bg-gray-800 text-white',
        default => 'bg-gray-100 text-gray-800',
    };
@endphp
<span class="px-2 py-1 text-xs rounded {{ $classes }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>