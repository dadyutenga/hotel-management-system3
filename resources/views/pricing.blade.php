<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing - MRK Hotel Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
    <meta name="description" content="Flexible pricing plans for MRK Hotel Management System. Choose the plan that fits your hotel's needs and budget.">
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
                    fontSize: {
                        'xs': '0.65rem',
                        'sm': '0.75rem',
                        'base': '0.8125rem',
                        'lg': '0.9375rem',
                        'xl': '1.0625rem',
                        '2xl': '1.25rem',
                        '3xl': '1.5rem',
                        '4xl': '1.875rem',
                        '5xl': '2.25rem',
                        '6xl': '2.75rem',
                    }
                },
            }
        };
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-white font-sans antialiased scroll-smooth">
@include('partials.public-header')

<!-- Main Content -->
<main class="overflow-hidden">
    <!-- Hero Section -->
    <section class="relative py-20 md:py-28 bg-gradient-to-br from-blue-50 via-white to-blue-50">
        <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
        <div class="container mx-auto px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <span class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-6 border border-primary/20">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-14a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415l-2.536-2.535V4z" clip-rule="evenodd"/></svg>
                    Simple, Transparent Pricing
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-secondary leading-tight mb-6">
                    Plans That Scale<br/>
                    <span class="bg-gradient-to-r from-primary via-blue-600 to-primary bg-clip-text text-transparent">With Your Business</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-600 leading-relaxed max-w-3xl mx-auto">
                    Start free for 30 days. No credit card required. Choose the plan that best fits your hotel's needs.
                </p>
            </div>
        </div>
    </section>

    <!-- Billing Toggle -->
    <section class="py-8 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-center gap-4" x-data="{ annual: true }">
                <span class="text-sm font-medium" :class="annual ? 'text-gray-500' : 'text-secondary'">Monthly</span>
                <button @click="annual = !annual" class="relative inline-flex h-8 w-16 items-center rounded-full transition-colors" :class="annual ? 'bg-primary' : 'bg-gray-300'">
                    <span class="inline-block h-6 w-6 transform rounded-full bg-white shadow transition-transform" :class="annual ? 'translate-x-9' : 'translate-x-1'"></span>
                </button>
                <span class="text-sm font-medium" :class="annual ? 'text-secondary' : 'text-gray-500'">Annual</span>
                <span class="ml-2 inline-flex items-center px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Save 20%</span>
            </div>
        </div>
    </section>

    <!-- Pricing Cards -->
    <section class="py-12 bg-white" x-data="{ annual: true }">
        <div class="container mx-auto px-6 lg:px-8">
            <!-- Toggle (hidden but connected) -->
            <div class="hidden">
                <button @click="annual = !annual"></button>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Starter Plan -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-secondary mb-2">Starter</h3>
                        <p class="text-gray-600 text-sm mb-6">Perfect for small hotels & guesthouses</p>
                        <div class="mb-6">
                            <span class="text-4xl font-extrabold text-secondary">$29</span>
                            <span class="text-gray-500">/month</span>
                            <p class="text-sm text-gray-500 mt-1">Up to 20 rooms</p>
                        </div>
                        <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center px-6 py-3 text-sm font-semibold rounded-lg border-2 border-primary text-primary hover:bg-primary hover:text-white transition-colors">
                            Start Free Trial
                        </a>
                    </div>
                    <div class="px-8 pb-8">
                        <p class="text-sm font-semibold text-secondary mb-4">Features include:</p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Reservation management
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Guest database
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Basic reporting
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Email support
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                2 user accounts
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Professional Plan -->
                <div class="bg-white rounded-2xl border-2 border-primary overflow-hidden shadow-xl relative">
                    <div class="absolute top-0 left-0 right-0 bg-primary text-white text-center py-2 text-sm font-semibold">
                        Most Popular
                    </div>
                    <div class="p-8 pt-14">
                        <h3 class="text-xl font-bold text-secondary mb-2">Professional</h3>
                        <p class="text-gray-600 text-sm mb-6">For growing hotels & boutique properties</p>
                        <div class="mb-6">
                            <span class="text-4xl font-extrabold text-primary">$79</span>
                            <span class="text-gray-500">/month</span>
                            <p class="text-sm text-gray-500 mt-1">Up to 50 rooms</p>
                        </div>
                        <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center px-6 py-3 text-sm font-semibold rounded-lg bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg hover:shadow-xl transition-shadow">
                            Start Free Trial
                        </a>
                    </div>
                    <div class="px-8 pb-8">
                        <p class="text-sm font-semibold text-secondary mb-4">Everything in Starter, plus:</p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Billing & invoicing
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Housekeeping management
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Mobile money integration
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Advanced reporting
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Phone & email support
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                10 user accounts
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Business Plan -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                    <div class="p-8">
                        <h3 class="text-xl font-bold text-secondary mb-2">Business</h3>
                        <p class="text-gray-600 text-sm mb-6">For established hotels & resorts</p>
                        <div class="mb-6">
                            <span class="text-4xl font-extrabold text-secondary">$149</span>
                            <span class="text-gray-500">/month</span>
                            <p class="text-sm text-gray-500 mt-1">Up to 100 rooms</p>
                        </div>
                        <a href="{{ route('register') }}" class="w-full inline-flex items-center justify-center px-6 py-3 text-sm font-semibold rounded-lg border-2 border-primary text-primary hover:bg-primary hover:text-white transition-colors">
                            Start Free Trial
                        </a>
                    </div>
                    <div class="px-8 pb-8">
                        <p class="text-sm font-semibold text-secondary mb-4">Everything in Professional, plus:</p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Inventory management
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Restaurant POS integration
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Channel manager
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                API access
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Priority support
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-600">
                                <svg class="h-5 w-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                25 user accounts
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Enterprise Plan -->
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-shadow text-white">
                    <div class="p-8">
                        <h3 class="text-xl font-bold mb-2">Enterprise</h3>
                        <p class="text-gray-400 text-sm mb-6">For hotel chains & large properties</p>
                        <div class="mb-6">
                            <span class="text-4xl font-extrabold">Custom</span>
                            <p class="text-sm text-gray-400 mt-1">Unlimited rooms</p>
                        </div>
                        <a href="{{ url('/contact') }}" class="w-full inline-flex items-center justify-center px-6 py-3 text-sm font-semibold rounded-lg bg-white text-gray-900 hover:bg-gray-100 transition-colors">
                            Contact Sales
                        </a>
                    </div>
                    <div class="px-8 pb-8">
                        <p class="text-sm font-semibold mb-4">Everything in Business, plus:</p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3 text-sm text-gray-300">
                                <svg class="h-5 w-5 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Multi-property management
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-300">
                                <svg class="h-5 w-5 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                White-label options
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-300">
                                <svg class="h-5 w-5 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Custom integrations
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-300">
                                <svg class="h-5 w-5 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                Dedicated account manager
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-300">
                                <svg class="h-5 w-5 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                On-site training
                            </li>
                            <li class="flex items-start gap-3 text-sm text-gray-300">
                                <svg class="h-5 w-5 text-green-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                SLA guarantee
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Comparison -->
    <section class="py-20 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4">
                    Compare Plans
                </span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-secondary mb-6">
                    Feature Comparison
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    See what's included in each plan
                </p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full min-w-[800px]">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-4 px-4 font-semibold text-secondary">Feature</th>
                            <th class="text-center py-4 px-4 font-semibold text-secondary">Starter</th>
                            <th class="text-center py-4 px-4 font-semibold text-primary">Professional</th>
                            <th class="text-center py-4 px-4 font-semibold text-secondary">Business</th>
                            <th class="text-center py-4 px-4 font-semibold text-secondary">Enterprise</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="py-4 px-4 text-gray-700">Reservation Management</td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="py-4 px-4 text-gray-700">Guest Database</td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 text-gray-700">Billing & Invoicing</td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-gray-300 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="py-4 px-4 text-gray-700">Housekeeping Management</td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-gray-300 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 text-gray-700">Mobile Money Integration</td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-gray-300 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="py-4 px-4 text-gray-700">Inventory Management</td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-gray-300 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-gray-300 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 text-gray-700">Channel Manager</td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-gray-300 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-gray-300 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="py-4 px-4 text-gray-700">Multi-Property Management</td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-gray-300 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-gray-300 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-gray-300 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg></td>
                            <td class="text-center py-4 px-4"><svg class="h-5 w-5 text-green-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4">
                    FAQ
                </span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-secondary mb-6">
                    Pricing Questions Answered
                </h2>
            </div>
            <div class="max-w-3xl mx-auto space-y-4" x-data="{ open: null }">
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="open = open === 1 ? null : 1" class="w-full px-6 py-5 text-left flex items-center justify-between">
                        <span class="font-semibold text-secondary">Can I change plans at any time?</span>
                        <svg class="h-5 w-5 text-primary transition-transform" :class="{ 'rotate-180': open === 1 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 1" x-collapse class="px-6 pb-5">
                        <p class="text-gray-600">Yes! You can upgrade or downgrade your plan at any time. When upgrading, you'll be prorated for the remainder of your billing cycle. Downgrades take effect at the end of your current billing period.</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="open = open === 2 ? null : 2" class="w-full px-6 py-5 text-left flex items-center justify-between">
                        <span class="font-semibold text-secondary">What happens after the free trial?</span>
                        <svg class="h-5 w-5 text-primary transition-transform" :class="{ 'rotate-180': open === 2 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 2" x-collapse class="px-6 pb-5">
                        <p class="text-gray-600">After your 30-day free trial, you can choose any paid plan to continue. Your data and settings will be preserved. If you don't upgrade, your account will be temporarily paused, but your data will be kept safe for 30 days.</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="open = open === 3 ? null : 3" class="w-full px-6 py-5 text-left flex items-center justify-between">
                        <span class="font-semibold text-secondary">Do you offer discounts for non-profits?</span>
                        <svg class="h-5 w-5 text-primary transition-transform" :class="{ 'rotate-180': open === 3 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 3" x-collapse class="px-6 pb-5">
                        <p class="text-gray-600">Yes, we offer special pricing for registered non-profit organizations. Contact our sales team with your non-profit documentation to learn more about available discounts.</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="open = open === 4 ? null : 4" class="w-full px-6 py-5 text-left flex items-center justify-between">
                        <span class="font-semibold text-secondary">Is there a setup fee?</span>
                        <svg class="h-5 w-5 text-primary transition-transform" :class="{ 'rotate-180': open === 4 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 4" x-collapse class="px-6 pb-5">
                        <p class="text-gray-600">No, there are no setup fees for any of our plans. For Enterprise customers requiring custom integrations or on-site training, there may be additional service fees which will be clearly communicated upfront.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-br from-primary via-blue-600 to-primary">
        <div class="container mx-auto px-6 lg:px-8 text-center">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white mb-6">
                    Start Your Free Trial Today
                </h2>
                <p class="text-xl text-white/90 mb-10 leading-relaxed">
                    30 days free. No credit card required. Cancel anytime.
                </p>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl bg-white text-primary shadow-lg hover:shadow-xl transition-shadow">
                    Get Started Now
                    <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>
        </div>
    </section>
</main>

@include('partials.public-footer')
</body>
</html>
