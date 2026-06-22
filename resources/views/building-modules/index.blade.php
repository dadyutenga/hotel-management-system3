@extends('layouts.app')

@section('title', 'Building Modules')
@section('page-title', 'Building Modules')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-secondary">Building Modules</h2>
            <p class="text-sm text-gray-500 mt-1">Manage restaurant and bar modules per building.</p>
        </div>
        <a href="{{ route('building-modules.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary to-blue-600 text-white text-sm font-semibold rounded-xl hover:shadow-lg transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Module
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gradient-to-r from-blue-50 to-white">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Building</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Type</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Name</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-primary uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($buildings as $building)
                    @foreach($building->modules as $module)
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-secondary">{{ $building->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $module->isRestaurant() ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ ucfirst($module->type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $module->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $module->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($module->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('building-modules.edit', $module) }}" class="text-primary hover:text-blue-700 font-semibold mr-3">Edit</a>
                            <form method="POST" action="{{ route('building-modules.destroy', $module) }}" class="inline" onsubmit="return confirm('Delete this module?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-700 font-semibold">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @if($building->modules->isEmpty())
                    <tr class="hover:bg-blue-50/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-secondary">{{ $building->name }}</td>
                        <td colspan="4" class="px-6 py-4 text-sm text-gray-400 italic">No modules configured.</td>
                    </tr>
                    @endif
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-16 text-center text-gray-500">
                        No buildings found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($buildings->hasPages())
    <div class="mt-6">
        {{ $buildings->links() }}
    </div>
    @endif
</div>
@endsection
