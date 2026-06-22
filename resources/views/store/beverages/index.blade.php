@extends('store.layout')

@section('content')
<div class="flex items-center justify-between mb-6" x-data="{ search: '{{ request('search') }}' }">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Beverages</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $beverages->total() }} registered beverages</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('store.beverages.categories') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition-colors">Categories</a>
        <a href="{{ route('store.beverages.create') }}" class="px-4 py-2 bg-primary hover:bg-blue-700 text-white rounded-xl text-sm font-medium transition-colors">+ Register Beverage</a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6 p-4">
    <form method="GET" action="{{ route('store.beverages.index') }}" class="flex gap-3 flex-wrap">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name..." class="flex-1 min-w-[200px] px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
        <select name="category" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-medium hover:bg-blue-700">Filter</button>
        @if(request()->hasAny(['search', 'category']))
            <a href="{{ route('store.beverages.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200">Clear</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Beverage</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Barcode</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Category</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Unit</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Buy Price</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Sell Price</th>
                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Stock</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($beverages as $beverage)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-3">
                    <div class="font-medium text-gray-900">{{ $beverage->name }}</div>
                    @if($beverage->description)
                        <div class="text-xs text-gray-400 truncate max-w-[200px]">{{ $beverage->description }}</div>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded font-mono">{{ $beverage->barcode }}</code>
                </td>
                <td class="px-4 py-3 text-sm text-gray-600">{{ $beverage->category?->name ?? '—' }}</td>
                <td class="px-4 py-3 text-sm text-gray-600 capitalize">{{ $beverage->unit }}</td>
                <td class="px-4 py-3 text-sm text-gray-600 text-right">{{ number_format($beverage->buying_price, 2) }}</td>
                <td class="px-4 py-3 text-sm text-gray-600 text-right">{{ number_format($beverage->selling_price, 2) }}</td>
                <td class="px-4 py-3 text-right">
                    @php $qty = $beverage->inventory?->quantity_on_hand ?? 0; @endphp
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $qty <= $beverage->reorder_level ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                        {{ $qty }}
                    </span>
                </td>
                <td class="px-4 py-3 text-center">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('store.beverages.show', $beverage) }}" class="text-primary hover:text-blue-700 text-sm font-medium">View</a>
                        <a href="{{ route('store.beverages.edit', $beverage) }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium">Edit</a>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="px-4 py-12 text-center text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    No beverages registered yet.
                    <a href="{{ route('store.beverages.create') }}" class="text-primary font-medium hover:underline">Register one</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $beverages->links() }}
</div>
@endsection
