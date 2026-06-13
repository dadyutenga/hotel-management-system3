@extends('store.layout')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Stock Receivings</h2>
    <p class="text-sm text-gray-500 mt-1">{{ $receivings->total() }} receiving records</p>
</div>

<div class="flex justify-end mb-4">
    <a href="{{ route('store.receivings.create') }}" class="px-4 py-2 bg-primary hover:bg-blue-700 text-white rounded-xl text-sm font-medium transition-colors">+ New Receiving</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Code</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Received By</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Supplier</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Items</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Date</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($receivings as $receiving)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-sm font-medium">{{ $receiving->receiving_code }}</td>
                <td class="px-4 py-3 text-sm">{{ $receiving->receiver->name }}</td>
                <td class="px-4 py-3 text-sm text-gray-500">{{ $receiving->supplier?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-center text-sm">{{ $receiving->items->sum('quantity') }}</td>
                <td class="px-4 py-3 text-sm text-gray-500">{{ $receiving->received_at->format('M d, Y H:i') }}</td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('store.receivings.show', $receiving) }}" class="text-primary hover:text-blue-700 text-sm font-medium">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-12 text-center text-gray-400">No receivings recorded yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $receivings->links() }}</div>
@endsection
