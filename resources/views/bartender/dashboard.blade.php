@extends('layouts.app')

@section('title', __('bartender.titles.dashboard'))
@section('page-title', __('bartender.titles.dashboard'))

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('bartender.stats.pending_orders') }}</p>
        <p class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['pending_orders'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('bartender.stats.damage_reports_today') }}</p>
        <p class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['damage_reports_today'] }}</p>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <p class="text-xs text-gray-500 uppercase tracking-wide">{{ __('bartender.stats.available_items') }}</p>
        <p class="text-2xl font-bold text-gray-800 mt-2">{{ $stats['available_items'] }}</p>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
    <h2 class="text-lg font-semibold text-gray-800 mb-3">{{ __('bartender.actions.quick_actions') }}</h2>
    <div class="flex flex-wrap gap-3 text-sm">
        <a href="{{ route('bartender.inbox') }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">{{ __('bartender.actions.open_inbox') }}</a>
        <a href="{{ route('bartender.stock') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">{{ __('bartender.actions.view_stock') }}</a>
        <a href="{{ route('bartender.pos') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">{{ __('bartender.actions.create_walkin') }}</a>
        <a href="{{ route('bartender.damage.create') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">{{ __('bartender.actions.report_damage') }}</a>
    </div>
</div>
@endsection
