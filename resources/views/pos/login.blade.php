<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff Login - Hotel Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { primary: '#005eb8', secondary: '#000000' },
                    fontFamily: { sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'] },
                }
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 min-h-screen font-sans antialiased flex items-center justify-center p-4">

<div x-data="passkeyLogin()" class="w-full max-w-md">
    <div class="text-center mb-8">
        <div class="inline-flex items-center gap-2 mb-4">
            <img src="{{ asset('images/header.png') }}" alt="HMS" class="h-10 w-auto brightness-0 invert" onerror="this.style.display='none'">
            <span class="text-xl font-extrabold text-white">Hotel Management</span>
        </div>
        <h1 class="text-2xl font-bold text-white">Staff Quick Login</h1>
        <p class="text-gray-400 text-sm mt-1">Select your name and enter your 4-digit PIN</p>
    </div>

    <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="p-6">
            @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600">
                {{ $errors->first() }}
            </div>
            @endif

            @if(session('locked_until'))
            <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600" x-data="lockoutTimer('{{ session('locked_until') }}')" x-init="startCountdown()">
                <div class="font-semibold mb-1">Account Locked</div>
                <div>Try again in <span x-text="remaining" class="font-mono font-bold">--:--</span></div>
            </div>
            @endif

            @if(session('attempts_remaining'))
            <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-xl text-sm text-yellow-700">
                Incorrect PIN. {{ session('attempts_remaining') }} attempts remaining.
            </div>
            @endif

            <form method="POST" action="{{ route('staff.login.submit') }}" x-ref="loginForm" @submit.prevent="submitForm()">
                @csrf
                <input type="hidden" name="user_id" :value="selectedUser">
                <input type="hidden" name="passkey" :value="pin">

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Select Staff</label>
                    <select x-model="selectedUser" @change="pin=''" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary">
                        <option value="">-- Choose --</option>
                        @foreach($staff as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->role->name ?? 'staff' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-5" x-show="selectedUser">
                    <div class="text-center mb-4">
                        <div class="text-lg font-semibold text-gray-800" x-text="selectedUserName()"></div>
                        <div class="flex justify-center gap-3 mt-3">
                            <template x-for="i in 4">
                                <div class="w-5 h-5 rounded-full border-2 transition-colors duration-150"
                                    :class="pin.length >= i ? 'bg-primary border-primary' : 'border-gray-300'"></div>
                            </template>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 max-w-[240px] mx-auto">
                        <template x-for="n in [1,2,3,4,5,6,7,8,9]">
                            <button type="button" @click="addDigit(n)"
                                class="h-14 rounded-xl bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-xl font-bold text-gray-800 transition-colors"
                                x-text="n"></button>
                        </template>
                        <button type="button" @click="pin=''"
                            class="h-14 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 text-sm font-semibold transition-colors">CLR</button>
                        <button type="button" @click="addDigit(0)"
                            class="h-14 rounded-xl bg-gray-100 hover:bg-gray-200 active:bg-gray-300 text-xl font-bold text-gray-800 transition-colors">0</button>
                        <button type="button" @click="removeDigit()"
                            class="h-14 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-600 transition-colors">
                            <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414-6.414a2 2 0 011.414-.586H19a2 2 0 012 2v10a2 2 0 01-2 2h-8.172a2 2 0 01-1.414-.586L3 12z"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" :disabled="pin.length !== 4 || !selectedUser"
                    class="w-full mt-5 px-4 py-3 rounded-xl font-semibold text-white transition-all"
                    :class="(pin.length === 4 && selectedUser) ? 'bg-primary hover:bg-blue-700 shadow-lg' : 'bg-gray-300 cursor-not-allowed'">
                    Login
                </button>
            </form>
        </div>

        <div class="bg-gray-50 px-6 py-4 text-center border-t border-gray-100">
            <a href="{{ route('login') }}" class="text-sm text-primary hover:text-blue-700 font-medium">Management Login &rarr;</a>
        </div>
    </div>
</div>

<script>
function passkeyLogin() {
    return {
        selectedUser: '',
        pin: '',
        staff: @json($staff->map(fn($u) => ['id' => $u->id, 'name' => $u->name])),

        addDigit(n) {
            if (this.pin.length < 4) this.pin += n.toString();
        },
        removeDigit() {
            this.pin = this.pin.slice(0, -1);
        },
        selectedUserName() {
            const found = this.staff.find(s => s.id === this.selectedUser);
            return found ? found.name : '';
        },
        submitForm() {
            if (this.pin.length === 4 && this.selectedUser) {
                this.$refs.loginForm.submit();
            }
        },
    };
}

function lockoutTimer(lockedUntil) {
    return {
        remaining: '--:--',
        startCountdown() {
            const target = new Date(lockedUntil).getTime();
            const update = () => {
                const diff = target - Date.now();
                if (diff <= 0) {
                    this.remaining = '00:00';
                    window.location.reload();
                    return;
                }
                const mins = Math.floor(diff / 60000);
                const secs = Math.floor((diff % 60000) / 1000);
                this.remaining = `${mins.toString().padStart(2,'0')}:${secs.toString().padStart(2,'0')}`;
                setTimeout(update, 1000);
            };
            update();
        },
    };
}
</script>
</body>
</html>
