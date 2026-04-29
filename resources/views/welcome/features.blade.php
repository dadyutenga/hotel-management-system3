<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Features - Hotel Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
    <meta name="description" content="Explore the powerful features of Hotel Management System - reservations, billing, housekeeping, inventory, and more.">
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
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                    Powerful Features
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-secondary leading-tight mb-6">
                    Everything You Need to<br/>
                    <span class="bg-gradient-to-r from-primary via-blue-600 to-primary bg-clip-text text-transparent">Run Your Hotel</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-600 leading-relaxed max-w-3xl mx-auto">
                    Comprehensive hotel management tools designed to streamline operations, enhance guest experiences, and maximize revenue.
                </p>
            </div>
        </div>
    </section>

    <!-- Main Features Grid -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1: Reservation Management -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                    <div class="h-56 overflow-hidden bg-gradient-to-br from-blue-100 to-blue-50">
                        <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?w=600&h=400&fit=crop" 
                             alt="Reservation Management" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="p-8">
                        <div class="bg-gradient-to-br from-primary to-blue-600 h-14 w-14 rounded-xl flex items-center justify-center mb-5 shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-3">Reservation Management</h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Streamline your booking process with our intuitive reservation system. Handle direct bookings, walk-ins, and OTA reservations all in one place.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Real-time availability calendar
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Automated confirmation emails
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Group booking management
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 2: Billing & Payments -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                    <div class="h-56 overflow-hidden bg-gradient-to-br from-green-100 to-green-50">
                        <img src="https://images.unsplash.com/photo-1563013544-824ae1b704d3?w=600&h=400&fit=crop" 
                             alt="Billing & Payments" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="p-8">
                        <div class="bg-gradient-to-br from-primary to-blue-600 h-14 w-14 rounded-xl flex items-center justify-center mb-5 shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-3">Billing & Payments</h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Accept payments locally and internationally with integrated payment processing. Support for mobile money, cards, and bank transfers.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                M-Pesa, Tigo Pesa, Airtel Money
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Visa & Mastercard support
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Automated invoicing
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 3: Housekeeping -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                    <div class="h-56 overflow-hidden bg-gradient-to-br from-purple-100 to-purple-50">
                        <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?w=600&h=400&fit=crop" 
                             alt="Housekeeping Management" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="p-8">
                        <div class="bg-gradient-to-br from-primary to-blue-600 h-14 w-14 rounded-xl flex items-center justify-center mb-5 shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-3">Housekeeping Management</h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Coordinate housekeeping tasks efficiently with real-time room status updates and task assignments for your cleaning staff.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Room status tracking
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Task assignment & scheduling
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Mobile app for staff
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 4: Inventory Management -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                    <div class="h-56 overflow-hidden bg-gradient-to-br from-orange-100 to-orange-50">
                        <img src="https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=600&h=400&fit=crop" 
                             alt="Inventory Management" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="p-8">
                        <div class="bg-gradient-to-br from-primary to-blue-600 h-14 w-14 rounded-xl flex items-center justify-center mb-5 shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-3">Inventory Management</h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Track stock levels, manage procurement, and get low-stock alerts. Keep your hotel supplies always available.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Real-time stock tracking
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Automated low-stock alerts
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Purchase order management
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 5: Reports & Analytics -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                    <div class="h-56 overflow-hidden bg-gradient-to-br from-cyan-100 to-cyan-50">
                        <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=600&h=400&fit=crop" 
                             alt="Reports & Analytics" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="p-8">
                        <div class="bg-gradient-to-br from-primary to-blue-600 h-14 w-14 rounded-xl flex items-center justify-center mb-5 shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-3">Reports & Analytics</h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Make data-driven decisions with comprehensive reports on occupancy, revenue, guest demographics, and operational efficiency.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Revenue & occupancy reports
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Custom report builder
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Export to Excel/PDF
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Feature 6: Channel Manager -->
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-lg hover:shadow-xl transition-shadow">
                    <div class="h-56 overflow-hidden bg-gradient-to-br from-pink-100 to-pink-50">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=600&h=400&fit=crop" 
                             alt="Channel Manager" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="p-8">
                        <div class="bg-gradient-to-br from-primary to-blue-600 h-14 w-14 rounded-xl flex items-center justify-center mb-5 shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-3">Channel Manager</h3>
                        <p class="text-gray-600 leading-relaxed mb-4">
                            Sync availability and rates with major OTAs like Booking.com, Expedia, and Airbnb from one central dashboard.
                        </p>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Two-way sync with OTAs
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Avoid double bookings
                            </li>
                            <li class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Unified rate management
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Additional Features Section -->
    <section class="py-20 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4">
                    And More
                </span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-secondary mb-6">
                    Additional Features
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Everything else you need to run a successful hotel
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Mini Feature Cards -->
                <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
                    <div class="bg-primary/10 h-12 w-12 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-secondary mb-2">Guest Profiles</h3>
                    <p class="text-sm text-gray-600">Store guest preferences and history for personalized service.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
                    <div class="bg-primary/10 h-12 w-12 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-secondary mb-2">Night Audit</h3>
                    <p class="text-sm text-gray-600">Automated end-of-day reconciliation and reporting.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
                    <div class="bg-primary/10 h-12 w-12 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-secondary mb-2">Wake-Up Calls</h3>
                    <p class="text-sm text-gray-600">Schedule automated wake-up calls for guests.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
                    <div class="bg-primary/10 h-12 w-12 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-secondary mb-2">Security</h3>
                    <p class="text-sm text-gray-600">Role-based access control and audit logs.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
                    <div class="bg-primary/10 h-12 w-12 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-secondary mb-2">Staff Management</h3>
                    <p class="text-sm text-gray-600">Manage employees, shifts, and permissions.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
                    <div class="bg-primary/10 h-12 w-12 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-secondary mb-2">Digital Contracts</h3>
                    <p class="text-sm text-gray-600">E-signatures for registration cards and agreements.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
                    <div class="bg-primary/10 h-12 w-12 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-secondary mb-2">Mobile App</h3>
                    <p class="text-sm text-gray-600">Manage your hotel on the go with our mobile app.</p>
                </div>

                <div class="bg-white rounded-xl p-6 shadow-md border border-gray-200">
                    <div class="bg-primary/10 h-12 w-12 rounded-lg flex items-center justify-center mb-4">
                        <svg class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-secondary mb-2">API Access</h3>
                    <p class="text-sm text-gray-600">Integrate with your existing systems via REST API.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Integration Partners -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4">
                    Integrations
                </span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-secondary mb-6">
                    Works With Your Favorite Tools
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Seamlessly connect with popular platforms and services
                </p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 flex items-center justify-center">
                    <span class="text-lg font-bold text-gray-400">Booking.com</span>
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 flex items-center justify-center">
                    <span class="text-lg font-bold text-gray-400">Expedia</span>
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 flex items-center justify-center">
                    <span class="text-lg font-bold text-gray-400">Airbnb</span>
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 flex items-center justify-center">
                    <span class="text-lg font-bold text-gray-400">M-Pesa</span>
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 flex items-center justify-center">
                    <span class="text-lg font-bold text-gray-400">Stripe</span>
                </div>
                <div class="bg-gray-50 rounded-xl border border-gray-200 p-6 flex items-center justify-center">
                    <span class="text-lg font-bold text-gray-400">QuickBooks</span>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-br from-primary via-blue-600 to-primary">
        <div class="container mx-auto px-6 lg:px-8 text-center">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white mb-6">
                    Ready to Transform Your Hotel Operations?
                </h2>
                <p class="text-xl text-white/90 mb-10 leading-relaxed">
                    Start your free 30-day trial today and experience all these features risk-free.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl bg-white text-primary shadow-lg hover:shadow-xl transition-shadow">
                        Start Free Trial
                        <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="{{ url('/pricing') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl border-2 border-white text-white hover:bg-white/10 transition-colors">
                        View Pricing
                    </a>
                </div>
                <p class="mt-6 text-white/80 text-sm">
                    No credit card required · Free for 30 days · Cancel anytime
                </p>
            </div>
        </div>
    </section>
</main>

@include('partials.public-footer')
</body>
</html>
