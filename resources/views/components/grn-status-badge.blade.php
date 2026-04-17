{{-- resources/views/components/grn-status-badge.blade.php --}}
@php
    $classes = match($status) {
        'draft' => 'bg-gray-100 text-gray-800',
        'submitted' => 'bg-indigo-100 text-indigo-800',
        'confirmed_by_storekeeper' => 'bg-yellow-100 text-yellow-800',
        'pending_manager_approval' => 'bg-amber-100 text-amber-800',
        'approved' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800',
    };

    $label = __('procurement.grn.statuses.' . $status);
@endphp

<span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $classes }}">
    {{ $label }}
</span>
