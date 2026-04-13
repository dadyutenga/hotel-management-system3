@extends('layouts.app')

@section('title', __('accountant.sidebar.audit_logs'))
@section('page-title', __('accountant.sidebar.audit_logs'))

@section('content')
<div class="grid gap-6 xl:grid-cols-2">
    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="text-xl font-extrabold text-secondary">{{ __('accountant.sections.journal_audit_trail') }}</h2>
        <div class="mt-4 space-y-3">
            @forelse($journalTrail as $entry)
                <div class="rounded-xl bg-gray-50 px-4 py-3"><div class="flex items-center justify-between"><div><div class="font-semibold text-secondary">{{ $entry->entry_no }}</div><div class="text-xs text-gray-500">{{ $entry->creator?->name ?? '-' }} / {{ $entry->poster?->name ?? '-' }}</div></div><div class="text-right"><div class="font-bold text-secondary"><x-money :amount="$entry->total_debit" /></div><div class="text-xs text-gray-500">{{ $entry->updated_at?->diffForHumans() }}</div></div></div></div>
            @empty
                <div class="text-sm text-gray-500">{{ __('general.no_data') }}</div>
            @endforelse
        </div>
    </div>
    <div class="rounded-2xl bg-white p-6 shadow-sm">
        <h2 class="text-xl font-extrabold text-secondary">{{ __('accountant.sections.system_alerts') }}</h2>
        <div class="mt-4 space-y-3">
            @forelse($logs as $log)
                <div class="rounded-xl bg-amber-50 px-4 py-3"><div class="font-semibold text-secondary">{{ $log->title }}</div><div class="mt-1 text-sm text-gray-600">{{ $log->body }}</div><div class="mt-2 text-xs text-gray-500">{{ $log->created_at?->diffForHumans() }}</div></div>
            @empty
                <div class="text-sm text-gray-500">{{ __('general.no_data') }}</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
