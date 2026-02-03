{{-- resources/views/components/reservation-status-badge.blade.php --}}
@php
    $classes = match($status) {
        'pending' => 'bg-yellow-100 text-yellow-800',
        'confirmed' => 'bg-green-100 text-green-800',
        'checked_in' => 'bg-blue-100 text-blue-800',
        'checked_out' => 'bg-gray-100 text-gray-800',
        'cancelled' => 'bg-red-100 text-red-800',
        'no_show' => 'bg-gray-100 text-gray-600',
        default => 'bg-gray-100 text-gray-800',
    };
@endphp
<span class="px-2 py-1 text-xs rounded {{ $classes }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>