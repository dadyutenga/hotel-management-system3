@extends('store.layout')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Stock-Takes</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $stockTakes->total() }} stock-take records</p>
    </div>
    <a href="{{ route('store.stock-takes.create') }}" class="px-4 py-2 bg-primary hover:bg-blue-700 text-white rounded-xl text-sm font-medium transition-colors">+ New Stock-Take</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Code</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Initiated By</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Status</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Items</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Started</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Completed</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($stockTakes as $stockTake)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-mono text-sm font-medium">{{ $stockTake->stock_take_code }}</td>
                <td class="px-4 py-3 text-sm">{{ $stockTake->initiator->name }}</td>
                <td class="px-4 py-3 text-center">
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                        {{ $stockTake->status === 'completed' ? 'bg-green-100 text-green-700' : ($stockTake->status === 'cancelled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ ucfirst($stockTake->status) }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center text-sm">{{ $stockTake->items_count }}</td>
                <td class="px-4 py-3 text-sm text-gray-500">{{ $stockTake->started_at?->format('M d, Y H:i') ?? '—' }}</td>
                <td class="px-4 py-3 text-sm text-gray-500">{{ $stockTake->completed_at?->format('M d, Y H:i') ?? '—' }}</td>
                <td class="px-4 py-3 text-center">
                    <a href="{{ route('store.stock-takes.show', $stockTake) }}" class="text-primary hover:text-blue-700 text-sm font-medium">View</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center text-gray-400">No stock-takes recorded yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $stockTakes->links() }}</div>
@endsection
