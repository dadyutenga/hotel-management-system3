<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - MRK Hotel Management System</title>
    <link rel="icon" type="image/png" href="{{ asset('images/header.png') }}">
    <meta name="description" content="Get in touch with MRK Hotel Management System. Our support team is available 24/7 to help you with any questions.">
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
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/></svg>
                    Get in Touch
                </span>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-secondary leading-tight mb-6">
                    We're Here to<br/>
                    <span class="bg-gradient-to-r from-primary via-blue-600 to-primary bg-clip-text text-transparent">Help You Succeed</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-600 leading-relaxed max-w-3xl mx-auto">
                    Have questions about MRK Hotel Management System? Our team is ready to assist you 24/7.
                </p>
            </div>
        </div>
    </section>

    <!-- Contact Information & Form -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-12">
                <!-- Contact Info Cards -->
                <div class="space-y-6">
                    <!-- Phone -->
                    <div class="bg-gradient-to-br from-blue-50 to-white rounded-2xl p-8 border border-gray-200 shadow-sm">
                        <div class="bg-gradient-to-br from-primary to-blue-600 h-14 w-14 rounded-xl flex items-center justify-center mb-5 shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-2">Phone Support</h3>
                        <p class="text-gray-600 mb-4">Available 24/7 for urgent queries</p>
                        <a href="tel:+255123456789" class="text-primary font-semibold hover:underline">+255 123 456 789</a>
                        <br>
                        <a href="tel:+254123456789" class="text-primary font-semibold hover:underline">+254 123 456 789</a>
                    </div>

                    <!-- Email -->
                    <div class="bg-gradient-to-br from-blue-50 to-white rounded-2xl p-8 border border-gray-200 shadow-sm">
                        <div class="bg-gradient-to-br from-primary to-blue-600 h-14 w-14 rounded-xl flex items-center justify-center mb-5 shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-2">Email Us</h3>
                        <p class="text-gray-600 mb-4">We respond within 2 hours</p>
                        <a href="mailto:support@mrkhotels.com" class="text-primary font-semibold hover:underline">support@mrkhotels.com</a>
                        <br>
                        <a href="mailto:sales@mrkhotels.com" class="text-primary font-semibold hover:underline">sales@mrkhotels.com</a>
                    </div>

                    <!-- Office Location -->
                    <div class="bg-gradient-to-br from-blue-50 to-white rounded-2xl p-8 border border-gray-200 shadow-sm">
                        <div class="bg-gradient-to-br from-primary to-blue-600 h-14 w-14 rounded-xl flex items-center justify-center mb-5 shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-secondary mb-2">Head Office</h3>
                        <p class="text-gray-600 mb-4">Visit us in person</p>
                        <p class="text-gray-700">MRK Tower, 5th Floor<br>
                        Samora Avenue<br>
                        Dar es Salaam, Tanzania</p>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl p-10 shadow-xl border border-gray-200">
                        <h2 class="text-2xl font-bold text-secondary mb-2">Send Us a Message</h2>
                        <p class="text-gray-600 mb-8">Fill out the form below and we'll get back to you as soon as possible.</p>
                        
                        <form action="#" method="POST" class="space-y-6">
                            @csrf
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">First Name</label>
                                    <input type="text" id="first_name" name="first_name" required
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="John">
                                </div>
                                <div>
                                    <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" required
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="Doe">
                                </div>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                                    <input type="email" id="email" name="email" required
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="john@example.com">
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                                    <input type="tel" id="phone" name="phone"
                                           class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                           placeholder="+255 123 456 789">
                                </div>
                            </div>

                            <div>
                                <label for="hotel_name" class="block text-sm font-semibold text-gray-700 mb-2">Hotel/Company Name</label>
                                <input type="text" id="hotel_name" name="hotel_name"
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors"
                                       placeholder="Your Hotel Name">
                            </div>

                            <div>
                                <label for="subject" class="block text-sm font-semibold text-gray-700 mb-2">Subject</label>
                                <select id="subject" name="subject" required
                                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors">
                                    <option value="">Select a subject</option>
                                    <option value="sales">Sales Inquiry</option>
                                    <option value="support">Technical Support</option>
                                    <option value="demo">Request a Demo</option>
                                    <option value="partnership">Partnership Opportunity</option>
                                    <option value="billing">Billing Question</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <div>
                                <label for="message" class="block text-sm font-semibold text-gray-700 mb-2">Message</label>
                                <textarea id="message" name="message" rows="5" required
                                          class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary/20 focus:border-primary transition-colors resize-none"
                                          placeholder="Tell us how we can help you..."></textarea>
                            </div>

                            <div class="flex items-start gap-3">
                                <input type="checkbox" id="newsletter" name="newsletter" 
                                       class="mt-1 h-4 w-4 rounded border-gray-300 text-primary focus:ring-primary">
                                <label for="newsletter" class="text-sm text-gray-600">
                                    I'd like to receive product updates, news, and promotional offers from MRK Hotels.
                                </label>
                            </div>

                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl bg-gradient-to-r from-primary to-blue-600 text-white shadow-lg hover:shadow-xl transition-shadow">
                                Send Message
                                <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Regional Offices -->
    <section class="py-20 bg-gradient-to-b from-gray-50 to-white">
        <div class="container mx-auto px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-4 py-2 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4">
                    Our Locations
                </span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-secondary mb-6">
                    Regional Offices Across East Africa
                </h2>
                <p class="text-lg text-gray-600 leading-relaxed">
                    With offices in major cities, we're always close to you
                </p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Tanzania -->
                <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full overflow-hidden border-4 border-primary/20">
                        <img src="https://flagcdn.com/w80/tz.png" alt="Tanzania" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-2">Tanzania</h3>
                    <p class="text-gray-600 text-sm mb-3">Headquarters</p>
                    <p class="text-gray-700 text-sm">MRK Tower, Samora Ave<br>Dar es Salaam</p>
                    <p class="text-primary font-medium mt-3">+255 123 456 789</p>
                </div>

                <!-- Kenya -->
                <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full overflow-hidden border-4 border-primary/20">
                        <img src="https://flagcdn.com/w80/ke.png" alt="Kenya" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-2">Kenya</h3>
                    <p class="text-gray-600 text-sm mb-3">Regional Office</p>
                    <p class="text-gray-700 text-sm">Westlands Business Park<br>Nairobi</p>
                    <p class="text-primary font-medium mt-3">+254 123 456 789</p>
                </div>

                <!-- Uganda -->
                <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full overflow-hidden border-4 border-primary/20">
                        <img src="https://flagcdn.com/w80/ug.png" alt="Uganda" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-2">Uganda</h3>
                    <p class="text-gray-600 text-sm mb-3">Regional Office</p>
                    <p class="text-gray-700 text-sm">Kololo Business Centre<br>Kampala</p>
                    <p class="text-primary font-medium mt-3">+256 123 456 789</p>
                </div>

                <!-- Rwanda -->
                <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-200 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full overflow-hidden border-4 border-primary/20">
                        <img src="https://flagcdn.com/w80/rw.png" alt="Rwanda" class="w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold text-secondary mb-2">Rwanda</h3>
                    <p class="text-gray-600 text-sm mb-3">Regional Office</p>
                    <p class="text-gray-700 text-sm">Kigali Heights<br>Kigali</p>
                    <p class="text-primary font-medium mt-3">+250 123 456 789</p>
                </div>
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
                    Frequently Asked Questions
                </h2>
            </div>
            <div class="max-w-3xl mx-auto space-y-4" x-data="{ open: null }">
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="open = open === 1 ? null : 1" class="w-full px-6 py-5 text-left flex items-center justify-between">
                        <span class="font-semibold text-secondary">How quickly can I get started with MRK Hotels?</span>
                        <svg class="h-5 w-5 text-primary transition-transform" :class="{ 'rotate-180': open === 1 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 1" x-collapse class="px-6 pb-5">
                        <p class="text-gray-600">You can start using MRK Hotels within minutes! Simply sign up for a free trial, and our onboarding team will guide you through the setup process. Most hotels are fully operational within 24-48 hours.</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="open = open === 2 ? null : 2" class="w-full px-6 py-5 text-left flex items-center justify-between">
                        <span class="font-semibold text-secondary">Do you offer training for hotel staff?</span>
                        <svg class="h-5 w-5 text-primary transition-transform" :class="{ 'rotate-180': open === 2 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 2" x-collapse class="px-6 pb-5">
                        <p class="text-gray-600">Yes! We provide comprehensive training for all subscription levels. This includes online video tutorials, documentation, and live training sessions. Enterprise customers receive on-site training as well.</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="open = open === 3 ? null : 3" class="w-full px-6 py-5 text-left flex items-center justify-between">
                        <span class="font-semibold text-secondary">Can I migrate data from my existing system?</span>
                        <svg class="h-5 w-5 text-primary transition-transform" :class="{ 'rotate-180': open === 3 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 3" x-collapse class="px-6 pb-5">
                        <p class="text-gray-600">Absolutely! Our team can help you migrate data from most hotel management systems. We handle guest records, reservations, billing history, and more. Contact our support team for a free migration assessment.</p>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <button @click="open = open === 4 ? null : 4" class="w-full px-6 py-5 text-left flex items-center justify-between">
                        <span class="font-semibold text-secondary">What payment methods do you accept?</span>
                        <svg class="h-5 w-5 text-primary transition-transform" :class="{ 'rotate-180': open === 4 }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open === 4" x-collapse class="px-6 pb-5">
                        <p class="text-gray-600">We accept multiple payment methods including credit/debit cards (Visa, Mastercard), mobile money (M-Pesa, Tigo Pesa, Airtel Money), bank transfers, and PayPal. Choose what works best for your business.</p>
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
                    Ready to Get Started?
                </h2>
                <p class="text-xl text-white/90 mb-10 leading-relaxed">
                    Start your free 30-day trial today. No credit card required.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl bg-white text-primary shadow-lg">
                        Start Free Trial
                        <svg class="ml-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="{{ url('/pricing') }}" class="inline-flex items-center justify-center px-8 py-4 text-base font-semibold rounded-xl border-2 border-white text-white">
                        View Pricing
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

@include('partials.public-footer')
</body>
</html>
