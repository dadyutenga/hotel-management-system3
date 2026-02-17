{{-- resources/views/components/booking-status-badge.blade.php --}}
{{-- Booking = active stay. Statuses: checked_in, checked_out, cancelled --}}
@php
    $classes = match($status) {
        'checked_in' => 'bg-blue-100 text-blue-800',
        'checked_out' => 'bg-gray-100 text-gray-800',
        'cancelled' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800',
    };
@endphp
<span class="px-2 py-1 text-xs rounded {{ $classes }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</span>
