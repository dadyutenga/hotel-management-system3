{{-- resources/views/components/laundry-status-badge.blade.php --}}
@php
    $classes = match($status) {
        'pending' => 'bg-yellow-100 text-yellow-800',
        'in_progress' => 'bg-blue-100 text-blue-800',
        'completed' => 'bg-purple-100 text-purple-800',
        'returned' => 'bg-green-100 text-green-800',
        default => 'bg-gray-100 text-gray-800',
    };
@endphp
<span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $classes }}">
    {{ ucfirst(str_replace('_', ' ', $status)) }}
</span>