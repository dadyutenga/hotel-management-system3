@extends('layouts.app')

@section('title', 'Damage Reports')
@section('page-title', 'Damage Reports')

@section('content')
<div class="bg-white rounded-xl border border-gray-100 shadow-sm">
    <div class="p-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-800">Bar Damage Audit Trail</h2>
        <a href="{{ route('bartender.damage.create') }}" class="px-3 py-2 rounded-lg bg-blue-600 text-white text-sm hover:bg-blue-700">Report Damage</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600">
                <tr>
                    <th class="px-4 py-3 text-left">Date/Time</th>
                    <th class="px-4 py-3 text-left">Item</th>
                    <th class="px-4 py-3 text-right">Qty</th>
                    <th class="px-4 py-3 text-left">Reason</th>
                    <th class="px-4 py-3 text-left">Notes</th>
                    <th class="px-4 py-3 text-left">Reported By</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($reports as $report)
                    <tr>
                        <td class="px-4 py-3 text-gray-500">{{ $report->reported_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $report->product->name }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format((float)$report->quantity, 3) }}</td>
                        <td class="px-4 py-3">{{ ucfirst($report->reason) }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $report->notes }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $report->reporter->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No damage reports yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">{{ $reports->links() }}</div>
</div>
@endsection
