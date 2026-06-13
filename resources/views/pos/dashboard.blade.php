@extends('pos.layout')

@section('page-title', 'POS Dashboard')

@section('header-actions')
<form method="POST" action="{{ route('pos.logout') }}">
    @csrf
    <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
        Logout
    </button>
</form>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center mb-6">
        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        </div>
        <h3 class="text-xl font-bold text-gray-800">POS Terminal Ready</h3>
        <p class="text-sm text-gray-500 mt-2">Logged in as <span class="font-medium text-primary">{{ $staffUser->name }}</span></p>
        <p class="text-xs text-gray-400 mt-1 uppercase tracking-wide">{{ $staffUser->role->name ?? 'staff' }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($staffUser->hasAnyRole(['waiter', 'bar_tender']))
        <a href="{{ route('pos.orders.create') }}"
           class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:border-primary hover:shadow-md transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition-colors">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">New Order</h4>
                    <p class="text-sm text-gray-500">Place an order for a guest or walk-in customer.</p>
                </div>
            </div>
        </a>
        @endif

        @if($staffUser->isCashier())
        <a href="{{ route('pos.cashier.index') }}"
           class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:border-primary hover:shadow-md transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">Open Orders</h4>
                    <p class="text-sm text-gray-500">View and settle orders placed by waiters.</p>
                </div>
            </div>
        </a>
        @endif

        <a href="{{ route('staff.login') }}"
           class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:border-primary hover:shadow-md transition-all group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center group-hover:bg-gray-200 transition-colors">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">Switch Staff</h4>
                    <p class="text-sm text-gray-500">Log out and let another staff member log in.</p>
                </div>
            </div>
        </a>
    </div>
</div>
@endsection
