@extends('layouts.app')

@section('title', 'Edit Building Module')
@section('page-title', 'Edit Building Module')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-2xl">
            <h2 class="text-xl font-extrabold text-secondary">Edit Building Module</h2>
            <p class="text-sm text-gray-500 mt-1">Update module status or name.</p>
        </div>

        <form method="POST" action="{{ route('building-modules.update', $module) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-secondary mb-2">Building</label>
                    <p class="text-sm text-gray-700">{{ $module->building->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-secondary mb-2">Module Type</label>
                    <p class="text-sm text-gray-700">{{ ucfirst($module->type) }}</p>
                </div>

                <div>
                    <label for="name" class="block text-sm font-semibold text-secondary mb-2">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $module->name) }}" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-semibold text-secondary mb-2">Code</label>
                    <input type="text" name="code" id="code" value="{{ old('code', $module->code) }}"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('code') border-red-500 @enderror">
                    @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-secondary mb-2">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status', $module->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $module->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('building-modules.index') }}" class="px-6 py-2.5 text-sm font-semibold text-secondary bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all">Cancel</a>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg transition-all">Update Module</button>
            </div>
        </form>
    </div>
</div>
@endsection
