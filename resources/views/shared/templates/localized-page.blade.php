@extends('layouts.app')

@section('title', __('module.page.title'))
@section('page-title', __('module.page.title'))

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-800">{{ __('module.page.heading') }}</h2>
        <p class="text-sm text-gray-500">{{ __('module.page.subtitle') }}</p>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <label class="block text-sm text-gray-600 mb-1">{{ __('module.fields.example') }}</label>
        <input type="text" class="w-full border-gray-300 rounded-lg text-sm px-3 py-2" placeholder="{{ __('module.placeholders.example') }}">
    </div>
</div>
@endsection
