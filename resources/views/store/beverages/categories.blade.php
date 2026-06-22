@extends('store.layout')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('store.beverages.index') }}" class="text-sm text-primary hover:text-blue-700 font-medium">&larr; Back to Beverages</a>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Beverage Categories</h2>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="font-semibold text-gray-800 mb-4">Add Category</h3>
        <form method="POST" action="{{ route('store.beverages.categories.store') }}" class="flex gap-3">
            @csrf
            <input type="text" name="name" required placeholder="Category name" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <input type="text" name="description" placeholder="Description (optional)" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
            <button type="submit" class="px-4 py-2 bg-primary hover:bg-blue-700 text-white rounded-lg text-sm font-medium">Add</button>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Description</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Beverages</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($categories as $cat)
                <tr>
                    <td class="px-4 py-3 font-medium text-gray-900">{{ $cat->name }}</td>
                    <td class="px-4 py-3 text-sm text-gray-500">{{ $cat->description ?? '—' }}</td>
                    <td class="px-4 py-3 text-center text-sm">{{ $cat->beverages_count }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium {{ $cat->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $cat->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-4 py-8 text-center text-gray-400">No categories yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
