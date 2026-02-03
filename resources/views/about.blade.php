<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - MRK Hotel Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
    <meta name="description" content="Learn about MRK Hotel Management System - the leading cloud-based hotel management software trusted by hotels across East Africa.">
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
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM5.172 9.172a4 4 0 115.656 5.656 4 4 0 01-5.656-5.656z" clip-rule="evenodd"/></svg>
                    About MRK Hotels
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-secondary leading-tight mb-6">
                    Transforming Hospitality<br/>
                    <span class="bg-gradient-to-r from-primary via-blue-600 to-primary bg-clip-text text-transparent">Across East Africa</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-600 leading-relaxed max-w-3xl mx-auto">
                    We're on a mission to empower hotels with cutting-edge technology that simplifies operations, enhances guest experiences, and drives growth.
                </p>
            </div>
        </div>
    </section>

    <!-- Our Story Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-16 items-center">
                <div>
                    <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4">
                        Our Story
                    </span>
                    <h2 class="text-3xl md:text-4xl font-extrabold text-secondary mb-6">
                        Built by Hoteliers, for Hoteliers
                    </h2>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        MRK Hotel Management System was born from a simple observation: hotels across East Africa were struggling with outdated, fragmented systems that didn't meet their unique needs. Our founders, having spent decades in the hospitality industry, set out to change that.
                    </p>
                    <p class="text-gray-600 leading-relaxed mb-6">
                        Today, we serve over 500 hotels across Tanzania, Kenya, Uganda, Rwanda, and beyond. Our cloud-based platform handles millions of reservations annually, processes payments in local currencies, and provides real-time insights that help hotel managers make better decisions.
                    </p>
                    <p class="text-gray-600 leading-relaxed">
                        We're not just a software company – we're partners invested in your success. Our local support team understands the challenges you face and is always ready to help.
                    </p>
                </div>
                <div class="relative">
                    <div class="absolute -inset-4 bg-gradient-to-tr from-primary/20 via-blue-600/10 to-transparent rounded-3xl blur-2xl"></div>
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=800&h=600&fit=crop" 
                         alt="MRK Hotels Team" 
                         class="relative rounded-2xl shadow-2xl w-full h-auto object-cover ring-1 ring-gray-200">
                </div>
            </div>
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="py-20 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white rounded-2xl p-10 shadow-lg border border-gray-200">
                    <div class="bg-gradient-to-br from-primary to-blue-600 h-16 w-16 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-secondary mb-4">Our Mission</h3>
                    <p class="text-gray-600 leading-relaxed">
                        To empower hospitality businesses across Africa with innovative, affordable, and locally-relevant technology solutions that streamline operations, enhance guest experiences, and drive sustainable growth.
                    </p>
                </div>
                <div class="bg-white rounded-2xl p-10 shadow-lg border border-gray-200">
                    <div class="bg-gradient-to-br from-primary to-blue-600 h-16 w-16 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-secondary mb-4">Our Vision</h3>
                    <p class="text-gray-600 leading-relaxed">
                        To become Africa's leading hotel technology provider, setting the standard for hospitality software that combines world-class features with deep local market understanding and exceptional customer support.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-extrabold text-secondary mb-6">
                    Our Impact in Numbers
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Real results from real hotels using MRK Hotel Management System
                </p>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center p-8 bg-gradient-to-br from-blue-50 to-white rounded-2xl border border-gray-200">
                    <div class="text-4xl md:text-5xl font-extrabold text-primary mb-2">500+</div>
                    <div class="text-gray-600 font-medium">Hotels Worldwide</div>
                </div>
                <div class="text-center p-8 bg-gradient-to-br from-blue-50 to-white rounded-2xl border border-gray-200">
                    <div class="text-4xl md:text-5xl font-extrabold text-primary mb-2">2M+</div>
                    <div class="text-gray-600 font-medium">Reservations Processed</div>
                </div>
                <div class="text-center p-8 bg-gradient-to-br from-blue-50 to-white rounded-2xl border border-gray-200">
                    <div class="text-4xl md:text-5xl font-extrabold text-primary mb-2">99.9%</div>
                    <div class="text-gray-600 font-medium">System Uptime</div>
                </div>
                <div class="text-center p-8 bg-gradient-to-br from-blue-50 to-white rounded-2xl border border-gray-200">
                    <div class="text-4xl md:text-5xl font-extrabold text-primary mb-2">24/7</div>
                    <div class="text-gray-600 font-medium">Customer Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-20 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4">
                    Our Leadership
                </span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-secondary mb-6">
                    Meet the Team Behind MRK Hotels
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    Industry veterans passionate about transforming hospitality in Africa
                </p>
            </div>
            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-200">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=400&h=400&fit=crop" 
                         alt="CEO" 
                         class="w-full h-64 object-cover">
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-bold text-secondary">Michael Karanja</h3>
                        <p class="text-primary font-medium mb-3">CEO & Co-Founder</p>
                        <p class="text-gray-600 text-sm">20+ years in hospitality technology. Former CTO at East African Hotels Group.</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-200">
                    <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&h=400&fit=crop" 
                         alt="CTO" 
                         class="w-full h-64 object-cover">
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-bold text-secondary">Rose Mwangi</h3>
                        <p class="text-primary font-medium mb-3">CTO & Co-Founder</p>
                        <p class="text-gray-600 text-sm">Software architect with 15+ years building enterprise solutions for hospitality.</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg border border-gray-200">
                    <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=400&h=400&fit=crop" 
                         alt="COO" 
                         class="w-full h-64 object-cover">
                    <div class="p-6 text-center">
                        <h3 class="text-xl font-bold text-secondary">David Odhiambo</h3>
                        <p class="text-primary font-medium mb-3">COO</p>
                        <p class="text-gray-600 text-sm">Former General Manager at Serena Hotels. Expert in hotel operations optimization.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4">
                    Our Values
                </span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-secondary mb-6">
                    What Drives Us Every Day
                </h2>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="bg-gradient-to-br from-primary to-blue-600 h-16 w-16 rounded-2xl flex items-center justify-center mb-5 mx-auto shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-secondary mb-2">Trust</h3>
                    <p class="text-gray-600 text-sm">We protect your data like it's our own and deliver on every promise we make.</p>
                </div>
                <div class="text-center">
                    <div class="bg-gradient-to-br from-primary to-blue-600 h-16 w-16 rounded-2xl flex items-center justify-center mb-5 mx-auto shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-secondary mb-2">Innovation</h3>
                    <p class="text-gray-600 text-sm">We continuously improve our platform based on customer feedback and industry trends.</p>
                </div>
                <div class="text-center">
                    <div class="bg-gradient-to-br from-primary to-blue-600 h-16 w-16 rounded-2xl flex items-center justify-center mb-5 mx-auto shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-secondary mb-2">Partnership</h3>
                    <p class="text-gray-600 text-sm">Your success is our success. We work alongside you to achieve your goals.</p>
                </div>
                <div class="text-center">
                    <div class="bg-gradient-to-br from-primary to-blue-600 h-16 w-16 rounded-2xl flex items-center justify-center mb-5 mx-auto shadow-lg">
                        <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-secondary mb-2">Local Focus</h3>
                    <p class="text-gray-600 text-sm">Built for African markets with local payment methods, languages, and support.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-br from-primary via-blue-600 to-primary">
        <div class="container mx-auto px-6 lg:px-8 text-center">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-3xl md:text-4xl lg:text-5xl font-extrabold text-white mb-6">
                    Ready to Join the MRK Family?
                </h2>
                <p class="text-xl text-white/90 mb-10 leading-relaxed">
                    Start your free 30-day trial and experience the difference a truly integrated hotel management system can make.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl bg-white text-primary shadow-lg">
                        Start Free Trial
                        <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="{{ url('/contact') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl border-2 border-white text-white">
                        Contact Sales
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

@include('partials.public-footer')
</body>
</html>
