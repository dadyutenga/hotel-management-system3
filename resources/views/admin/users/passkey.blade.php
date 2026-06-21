@extends('layouts.app')

@section('page-title', 'Manage Passkey')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('users.index') }}" class="text-sm text-primary hover:text-blue-700 font-medium">&larr; Back to Users</a>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Passkey Management: {{ $user->name }}</h2>
        <p class="text-sm text-gray-500">{{ $user->email }} &middot; {{ $user->role->name ?? 'No role' }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="font-semibold text-gray-800 mb-4">Passkey Status</h3>
        <div class="flex items-center justify-between mb-4">
            <div>
                <div class="text-sm font-medium text-gray-700">Passkey Login</div>
                <div class="text-xs text-gray-500">{{ $user->passkey_enabled ? 'Enabled' : 'Disabled' }}</div>
            </div>
            <form method="POST" action="{{ route('users.passkey.toggle', $user) }}">
                @csrf
                <input type="hidden" name="passkey_enabled" value="{{ $user->passkey_enabled ? 0 : 1 }}">
                <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $user->passkey_enabled ? 'bg-primary' : 'bg-gray-300' }}">
                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $user->passkey_enabled ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
            </form>
        </div>

        @if($user->isPasskeyLocked())
        <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600 mb-4">
            <div class="font-semibold">Account Locked</div>
            <div>Locked until {{ $user->passkey_locked_until->format('M d, Y H:i') }}</div>
            <form method="POST" action="{{ route('users.passkey.unlock', $user) }}" class="mt-2">
                @csrf
                <button type="submit" class="text-sm font-medium text-red-700 underline">Unlock Now</button>
            </form>
        </div>
        @endif

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">Role</div>
                <div class="font-medium mt-1">{{ $user->displayRoleName() }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">Last Passkey Login</div>
                <div class="font-medium mt-1">{{ $user->last_passkey_login?->format('M d, Y H:i') ?? 'Never' }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">Failed Attempts</div>
                <div class="font-medium mt-1">{{ $user->failed_passkey_attempts }}</div>
            </div>
            <div class="bg-gray-50 rounded-lg p-3">
                <div class="text-xs text-gray-500">Property Code</div>
                <div class="font-medium mt-1">{{ $user->building?->code ?? '—' }}</div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Reset Passkey</h3>
        <form method="POST" action="{{ route('users.passkey.reset', $user) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New 4-Digit PIN</label>
                <input type="text" name="passkey" maxlength="4" pattern="[0-9]{4}" required
                    class="w-32 px-3 py-2.5 border border-gray-300 rounded-lg text-lg font-mono text-center tracking-widest focus:ring-2 focus:ring-primary/20 focus:border-primary">
                @error('passkey') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm PIN</label>
                <input type="text" name="passkey_confirmation" maxlength="4" pattern="[0-9]{4}" required
                    class="w-32 px-3 py-2.5 border border-gray-300 rounded-lg text-lg font-mono text-center tracking-widest focus:ring-2 focus:ring-primary/20 focus:border-primary">
            </div>
            <button type="submit" class="px-4 py-2 bg-primary hover:bg-blue-700 text-white rounded-xl text-sm font-semibold transition-colors"
                onclick="return confirm('Reset this user passkey?')">
                Reset Passkey
            </button>
        </form>
    </div>
</div>
@endsection
