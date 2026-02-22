{{-- resources/views/dashboards/waiter.blade.php --}}
@extends('layouts.app')

@section('title', 'Waiter Dashboard - MRK Hotel')
@section('page-title', 'Waiter Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="bg-gradient-to-r from-violet-500 to-purple-600 rounded-2xl p-6 mb-8 text-white shadow-xl">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold mb-2">Welcome, {{ auth()->user()->name }}!</h2>
            <p class="text-violet-100">Your activity overview for today.</p>
        </div>
        <div class="hidden md:block text-right">
            <p class="text-sm text-violet-200">{{ now()->format('l, F d, Y') }}</p>
            <p class="text-3xl font-extrabold">{{ now()->format('h:i A') }}</p>
        </div>
    </div>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Pending Requests</p>
                <p class="text-3xl font-extrabold text-yellow-600 mt-1">{{ $stats['pending_requests'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500">Total Requests</p>
                <p class="text-3xl font-extrabold text-secondary mt-1">{{ $stats['my_requests'] }}</p>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-violet-50 to-violet-100 rounded-xl flex items-center justify-center">
                <svg class="w-7 h-7 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- My Recent Requests -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-extrabold text-secondary">My Recent Requests</h3>
        <a href="{{ route('store.internal-requests.index') }}" class="text-primary text-sm hover:underline">View all</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-600 font-semibold">Date</th>
                    <th class="px-4 py-3 text-left text-gray-600 font-semibold">Product</th>
                    <th class="px-4 py-3 text-right text-gray-600 font-semibold">Qty</th>
                    <th class="px-4 py-3 text-center text-gray-600 font-semibold">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($myRequests as $req)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $req->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3 font-medium">{{ $req->product->name }}</td>
                    <td class="px-4 py-3 text-right">{{ $req->quantity }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            @if($req->status === 'pending')   bg-yellow-100 text-yellow-700
                            @elseif($req->status === 'approved')  bg-blue-100 text-blue-700
                            @elseif($req->status === 'fulfilled') bg-green-100 text-green-700
                            @elseif($req->status === 'rejected')  bg-red-100 text-red-600
                            @else bg-gray-100 text-gray-500 @endif">
                            {{ ucfirst($req->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-8 text-center text-gray-400">No requests yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Quick Action -->
<div class="mt-6">
    <a href="{{ route('store.internal-requests.create') }}" class="inline-flex items-center gap-3 bg-primary text-white px-6 py-3 rounded-2xl shadow-lg hover:bg-blue-700 transition font-medium">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        New Internal Request
    </a>
</div>
@endsection
