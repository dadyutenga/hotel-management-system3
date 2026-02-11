{{-- resources/views/laundry/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Laundry Management')
@section('page-title', 'Laundry Management')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Laundry Tasks</h2>
            <p class="text-sm text-gray-500 mt-1">Manage guest laundry services</p>
        </div>
        @if(auth()->user()->isSupervisor() || auth()->user()->isAdmin())
        <a href="{{ route('laundry.create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Laundry Task
        </a>
        @endif
    </div>

    <!-- Filter Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $tasks->where('status', 'pending')->count() }}</div>
                    <div class="text-xs text-gray-500">Pending</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $tasks->where('status', 'in_progress')->count() }}</div>
                    <div class="text-xs text-gray-500">In Progress</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $tasks->where('status', 'completed')->count() }}</div>
                    <div class="text-xs text-gray-500">Completed</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition-shadow cursor-pointer">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-900">{{ $tasks->where('status', 'returned')->count() }}</div>
                    <div class="text-xs text-gray-500">Returned</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tasks Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guest</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Room</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($tasks as $task)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center text-white text-xs font-bold">
                                    {{ substr($task->task_number, -4) }}
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $task->task_number }}</div>
                                    <div class="text-xs text-gray-500">{{ $task->created_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $task->guest_name }}</div>
                            <div class="text-xs text-gray-500">{{ $task->reservation->reservation_number }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">{{ $task->room_number }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $task->assignedTo->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @include('components.laundry-status-badge', ['status' => $task->status])
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $task->is_amenity ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $task->is_amenity ? 'Amenity' : 'Paid' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-medium text-gray-900">
                                {{ $task->is_amenity ? 'Free' : '$' . number_format($task->cost, 2) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center gap-2">
                                @if(auth()->user()->isSupervisor() || auth()->user()->isAdmin())
                                    <a href="{{ route('laundry.edit', $task) }}" class="text-blue-600 hover:text-blue-900 font-medium">Edit</a>
                                    
                                    @if($task->status === 'pending')
                                        <form method="POST" action="{{ route('laundry.mark-in-progress', $task) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-purple-600 hover:text-purple-900 font-medium">Start</button>
                                        </form>
                                    @elseif($task->status === 'in_progress')
                                        <form method="POST" action="{{ route('laundry.mark-completed', $task) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900 font-medium">Complete</button>
                                        </form>
                                    @endif
                                @endif
                                
                                @if($task->status === 'completed' && (auth()->user()->isHouseHelp() || auth()->user()->isSupervisor() || auth()->user()->isAdmin()))
                                    @if(!auth()->user()->isHouseHelp() || $task->assigned_to === auth()->id())
                                        <form method="POST" action="{{ route('laundry.mark-returned', $task) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900 font-medium">Mark Returned</button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No laundry tasks</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new laundry task.</p>
                            @if(auth()->user()->isSupervisor() || auth()->user()->isAdmin())
                            <div class="mt-6">
                                <a href="{{ route('laundry.create') }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    New Laundry Task
                                </a>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($tasks->hasPages())
    <div class="mt-6">
        {{ $tasks->links() }}
    </div>
    @endif
</div>
@endsection