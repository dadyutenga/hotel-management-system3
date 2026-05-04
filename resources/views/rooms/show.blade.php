@extends('layouts.app')

@section('title', 'Room ' . $room->room_number)
@section('page-title', 'Room ' . $room->room_number)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">{{ $room->room_number }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ $room->floor->building->name ?? '' }} — {{ $room->floor->name ?? '' }}</p>
        </div>
        <a href="{{ route('rooms.index') }}" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors">
            ← Back to Rooms
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Room Info --}}
        <div class="md:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Room Details</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="text-xs text-gray-400 uppercase font-semibold">Room Number</div>
                    <div class="text-sm font-semibold text-secondary">{{ $room->room_number }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase font-semibold">Room Type</div>
                    <div class="text-sm font-semibold text-secondary">{{ $room->roomType->name ?? '—' }} ({{ $room->roomType->code ?? '' }})</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase font-semibold">Building</div>
                    <div class="text-sm font-semibold text-secondary">{{ $room->floor->building->name ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase font-semibold">Floor</div>
                    <div class="text-sm font-semibold text-secondary">{{ $room->floor->name ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase font-semibold">Rate</div>
                    <div class="text-sm font-bold text-secondary">{{ $room->roomType->formatted_rate ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase font-semibold">Status</div>
                    <div>@include('components.room-status-badge', ['status' => $room->status])</div>
                </div>
                <div>
                    <div class="text-xs text-gray-400 uppercase font-semibold">Active</div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $room->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $room->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <div>
                    @if(in_array($room->status, ['available', 'dirty']))
                    <button onclick="document.getElementById('ooo-modal').classList.remove('hidden')" class="px-4 py-2 bg-orange-600 text-white text-sm font-semibold rounded-xl hover:bg-orange-700 transition-colors">
                        Mark Out of Order
                    </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Maintenance / Cleaning Status --}}
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">
                @if($room->status === 'out_of_order') Maintenance Status @else Assignment Status @endif
            </h3>

            @if($room->status === 'out_of_order')
                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-gray-400 uppercase font-semibold">Reason</div>
                        <div class="text-sm text-red-600 font-medium">{{ $room->out_of_order_reason }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase font-semibold">Set By</div>
                        <div class="text-sm text-secondary">{{ $room->outOfOrderBy?->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase font-semibold">Set At</div>
                        <div class="text-sm text-secondary">{{ $room->out_of_order_set_at?->format('d M Y H:i') ?? '—' }}</div>
                    </div>
                    <div class="border-t pt-3">
                        <div class="text-xs text-gray-400 uppercase font-semibold">Assigned To</div>
                        <div class="text-sm text-secondary">{{ $room->cleaningAssignee->name ?? 'Not assigned' }}</div>
                    </div>
                    @if($room->cleaning_assigned_at)
                    <div>
                        <div class="text-xs text-gray-400 uppercase font-semibold">Assigned At</div>
                        <div class="text-sm text-secondary">{{ $room->cleaning_assigned_at->format('d M Y H:i') }}</div>
                    </div>
                    @endif
                    @if($room->cleaning_completed_at)
                    <div>
                        <div class="text-xs text-gray-400 uppercase font-semibold">Completed At</div>
                        <div class="text-sm text-green-600 font-semibold">{{ $room->cleaning_completed_at->format('d M Y H:i') }}</div>
                    </div>
                    @endif
                    @if($room->cleaning_confirmed_at)
                    <div>
                        <div class="text-xs text-gray-400 uppercase font-semibold">Confirmed By</div>
                        <div class="text-sm text-secondary">{{ $room->cleaningConfirmer->name ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-400 uppercase font-semibold">Confirmed At</div>
                        <div class="text-sm text-secondary">{{ $room->cleaning_confirmed_at->format('d M Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            @elseif($room->status === 'dirty')
                <div class="space-y-3">
                    <div>
                        <div class="text-xs text-gray-400 uppercase font-semibold">Assigned To</div>
                        <div class="text-sm text-secondary">{{ $room->cleaningAssignee->name ?? 'Not assigned' }}</div>
                    </div>
                    @if($room->cleaning_assigned_at)
                    <div>
                        <div class="text-xs text-gray-400 uppercase font-semibold">Assigned At</div>
                        <div class="text-sm text-secondary">{{ $room->cleaning_assigned_at->format('d M Y H:i') }}</div>
                    </div>
                    @endif
                    @if($room->cleaning_completed_at)
                    <div>
                        <div class="text-xs text-gray-400 uppercase font-semibold">Completed At</div>
                        <div class="text-sm text-green-600 font-semibold">{{ $room->cleaning_completed_at->format('d M Y H:i') }}</div>
                    </div>
                    @endif
                </div>
            @else
                <p class="text-sm text-gray-400">No cleaning or maintenance in progress.</p>
            @endif
        </div>
    </div>

    {{-- Out of Order Modal --}}
    @if(in_array($room->status, ['available', 'dirty']))
    <div id="ooo-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black bg-opacity-40" onclick="this.parentElement.classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6">
            <h3 class="text-lg font-bold text-secondary mb-4">Mark Out of Order — Room {{ $room->room_number }}</h3>
            <form method="POST" action="{{ route('rooms.out-of-order', $room) }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-secondary mb-2">Reason <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary transition-all" placeholder="e.g. plumbing issue, electrical fault, deep cleaning required..."></textarea>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="px-4 py-2 border border-gray-200 text-gray-600 text-sm font-semibold rounded-xl hover:bg-gray-50">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white text-sm font-semibold rounded-xl hover:bg-orange-700">Mark Out of Order</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
