<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Staff Login - Hotel Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
    <meta name="description" content="Staff PIN login for the hotel management system.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#005eb8',
                        secondary: '#000000',
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
                    },
                }
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-white font-sans antialiased">
    <div class="min-h-screen flex" x-data="passkeyLogin()">
        <!-- Left Side - Hotel Image Branding -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
            <div class="absolute inset-0">
                <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&h=1600&fit=crop"
                     alt="Hotel Management System" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-secondary/90 via-secondary/50 to-primary/30"></div>
            </div>
            <div class="relative z-10 flex flex-col justify-between p-12 w-full">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/header.png') }}" alt="Hotel Management System" class="h-12 w-auto brightness-0 invert" onerror="this.style.display='none'">
                    <div>
                        <span class="text-2xl font-extrabold text-white block leading-tight">Hotel Management</span>
                        <span class="text-xs text-blue-200 tracking-wider uppercase font-medium">System</span>
                    </div>
                </a>

                <!-- Content -->
                <div class="max-w-md">
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-white/10 text-white text-sm font-semibold rounded-full mb-6 border border-white/20">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"></path></svg>
                        Staff Portal
                    </span>
                    <h1 class="text-4xl font-extrabold text-white mb-6 leading-tight">
                        {{ __('auth.login.hotel_management_system') }}
                    </h1>
                    <p class="text-lg text-gray-200 leading-relaxed mb-8">
                        Quick PIN-based access for waiters, cashiers, and bartenders.
                    </p>
                    <div class="flex items-center gap-6 flex-wrap">
                        <div class="flex items-center gap-2 text-gray-200">
                            <svg class="w-5 h-5 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm font-medium">Order Entry</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-200">
                            <svg class="w-5 h-5 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm font-medium">POS Settlement</span>
                        </div>
                        <div class="flex items-center gap-2 text-gray-200">
                            <svg class="w-5 h-5 text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm font-medium">Quick Access</span>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <p class="text-gray-300 text-sm">
                    &copy; {{ date('Y') }} Hotel Management System. {{ __('auth.login.all_rights_reserved') }}
                </p>
            </div>
        </div>

        <!-- Right Side - PIN Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gradient-to-br from-blue-50 via-white to-blue-50">
            <div class="w-full max-w-md">
                <!-- Language Switcher -->
                <div class="flex justify-end mb-4">
                    <div class="flex items-center gap-2 text-sm">
                        <a href="{{ url('language/en') }}" class="flex items-center gap-1 px-2 py-1 rounded {{ app()->getLocale() === 'en' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            <span>🇬🇧</span> EN
                        </a>
                        <a href="{{ url('language/sw') }}" class="flex items-center gap-1 px-2 py-1 rounded {{ app()->getLocale() === 'sw' ? 'bg-primary text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                            <span>🇹🇿</span> SW
                        </a>
                    </div>
                </div>

                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                        <img src="{{ asset('images/header.png') }}" alt="Hotel Management System" class="h-12 w-auto" onerror="this.style.display='none'">
                        <div>
                            <span class="text-xl font-extrabold text-secondary block leading-tight">Hotel Management</span>
                            <span class="text-xs text-gray-500 tracking-wider uppercase font-medium">System</span>
                        </div>
                    </a>
                </div>

                <div class="text-center mb-10">
                    <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4 border border-primary/20">
                        Staff Quick Login
                    </span>
                    <h2 class="text-3xl font-extrabold text-secondary mb-2">Enter Your PIN</h2>
                    <p class="text-gray-600">Enter your email and 4-digit PIN</p>
                </div>

                @if($errors->any())
                <div class="mb-6 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    {{ $errors->first() }}
                </div>
                @endif

                @if(session('locked_until'))
                <div class="mb-6 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-600" x-data="lockoutTimer('{{ session('locked_until') }}')" x-init="startCountdown()">
                    <div class="font-semibold mb-1">Account Locked</div>
                    <div>Try again in <span x-text="remaining" class="font-mono font-bold">--:--</span></div>
                </div>
                @endif

                @if(session('attempts_remaining'))
                <div class="mb-6 p-3 bg-yellow-50 border border-yellow-200 rounded-xl text-sm text-yellow-700 flex items-center gap-2">
                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    Incorrect PIN. {{ session('attempts_remaining') }} attempts remaining.
                </div>
                @endif

                <form method="POST" action="{{ route('staff.login.submit') }}" x-ref="loginForm" @submit.prevent="submitForm()">
                    @csrf
                    <input type="hidden" name="passkey" :value="pin">

                    <div class="mb-6">
                        <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors text-gray-900 placeholder-gray-400"
                               placeholder="you@hotel.com"
                               @input="emailEntered = $event.target.value.trim().length > 0">
                    </div>

                    <div x-show="emailEntered" x-cloak>
                        <div class="text-center mb-4">
                            <div class="flex justify-center gap-3 mt-3">
                                <template x-for="i in 4">
                                    <div class="w-5 h-5 rounded-full border-2 transition-colors duration-150"
                                        :class="pin.length >= i ? 'bg-primary border-primary' : 'border-gray-300'"></div>
                                </template>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 max-w-[240px] mx-auto mb-6">
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

                    <button type="submit" :disabled="pin.length !== 4 || !emailEntered"
                        class="w-full px-6 py-3.5 text-base font-semibold rounded-xl transition-all"
                        :class="(pin.length === 4 && emailEntered) ? 'bg-gradient-to-r from-primary to-blue-600 hover:from-blue-700 hover:to-primary text-white shadow-lg hover:shadow-xl' : 'bg-gray-300 text-gray-500 cursor-not-allowed'">
                        Login
                    </button>
                </form>

                <!-- Management Login Link -->
                <div class="mt-6 text-center">
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-primary hover:text-blue-700 transition-colors">
                        &larr; Management? Login with email
                    </a>
                </div>

                <!-- Guest Booking Notice -->
                <div class="mt-8 p-5 bg-white rounded-2xl border border-gray-200 shadow-lg">
                    <div class="flex items-start gap-4">
                        <div class="bg-primary/10 p-3 rounded-xl">
                            <svg class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-secondary mb-1">{{ __('auth.guest_booking.title') }}</p>
                            <p class="text-sm text-gray-600 mb-2">{{ __('auth.guest_booking.description') }}</p>
                            <a href="{{ url('/contact') }}" class="inline-flex items-center text-sm font-semibold text-primary hover:text-blue-700 transition-colors">
                                {{ __('auth.guest_booking.link') }}
                                <svg class="ml-1 w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <p class="mt-6 text-center text-xs text-gray-500">
                    {{ __('auth.terms.by_signing_in') }}
                    <a href="{{ url('/terms') }}" class="text-primary hover:underline font-medium">{{ __('auth.terms.terms_of_service') }}</a>
                    {{ __('auth.terms.and') }}
                    <a href="{{ url('/privacy') }}" class="text-primary hover:underline font-medium">{{ __('auth.terms.privacy_policy') }}</a>.
                </p>
            </div>
        </div>
    </div>

<script>
function passkeyLogin() {
    return {
        emailEntered: '{{ old('email') }}'.length > 0,
        pin: '',

        addDigit(n) {
            if (this.pin.length < 4) this.pin += n.toString();
        },
        removeDigit() {
            this.pin = this.pin.slice(0, -1);
        },
        submitForm() {
            if (this.pin.length === 4 && this.emailEntered) {
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
