<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Request an invitation to join ChamaConnect">
    <title>Request Invitation - ChamaConnect</title>
    <link rel="icon" type="image/svg+xml" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
    <link rel="alternate icon" type="image/png" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
    <link rel="shortcut icon" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
    <meta name="theme-color" content="#0F766E">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{
            --primary: #0F766E; /* teal */
            --accent: #F59E0B; /* amber/gold */
            --sky: #0EA5E9; /* sky blue */
            --muted-cream: #F9FAF5; /* light background */
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px) saturate(1.1);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-white to-[var(--muted-cream)] text-slate-900 antialiased">
    <!-- Navigation -->
    <header class="sticky top-0 z-50 glass-effect border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('brand/chamaconnect-logo.svg') }}" alt="ChamaConnect" class="w-14 h-14 rounded-full shadow-lg group-hover:shadow-xl transition-all border-2 border-white ring-2 ring-[var(--primary)]/20 group-hover:ring-[var(--primary)]/40"/>
                    <div class="flex flex-col justify-center">
                        <h1 class="text-xl font-bold bg-gradient-to-r from-[var(--primary)] via-[var(--accent)] to-[var(--primary)] bg-clip-text text-transparent">ChamaConnect</h1>
                        <p class="text-xs text-slate-600 hidden sm:block font-medium">SACCOs • Fundraising • Meetings • M‑Pesa</p>
                    </div>
                </a>
                <nav class="flex items-center gap-4">
                    <a href="{{ url('/login') }}" class="inline-flex items-center px-4 py-2 bg-white text-[var(--primary)] border border-[var(--primary)]/30 rounded-lg font-semibold hover:bg-[var(--primary)]/5 transition-colors">Sign In</a>
                    <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[var(--forest-green)] to-[var(--brand-brown)] text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                        Back to Home
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen py-20">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="text-center mb-12">
                <h1 class="text-4xl sm:text-5xl font-bold text-slate-900 mb-4">
                    Request Access to ChamaConnect
                </h1>
                <p class="text-xl text-slate-600 mb-6">
                    Join Kenyan SACCOs, organizations, and communities using ChamaConnect for transparent fundraising, meetings, and M-Pesa payments
                </p>
                <div class="bg-teal-50 border border-teal-200 rounded-xl p-4 max-w-2xl mx-auto">
                    <p class="text-sm text-teal-900 font-medium">
                        <strong>Who can join?</strong> SACCOs, Chamas, community organizations, corporate teams, and families looking for transparent financial management and collaboration tools.
                    </p>
                </div>
            </div>

            <!-- Invite Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-200">
                <div class="mb-6">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="text-sm text-slate-600">
                            <p class="font-semibold text-slate-900 mb-1">How it works</p>
                            <ul class="list-disc list-inside space-y-1 ml-2">
                                <li>Admin reviews your request</li>
                                <li>You receive an email invitation (valid for 48 hours)</li>
                                <li>Complete your registration</li>
                                <li>Start managing tasks and collaborating with your team</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <form id="inviteForm" class="space-y-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-900 mb-2">
                            Full Name
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            required
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--forest-green)] focus:border-transparent transition-all"
                            placeholder="John Doe"
                        >
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-900 mb-2">
                            Institutional Email
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--forest-green)] focus:border-transparent transition-all"
                            placeholder="john@institution.ac.ke"
                        >
                        <p class="mt-2 text-sm text-slate-500">
                            Use your institutional email address (e.g., .ac.ke, .co.ke domains)
                        </p>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold text-slate-900 mb-2">
                            Phone Number
                        </label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            required
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--forest-green)] focus:border-transparent transition-all"
                            placeholder="254712345678"
                            pattern="[0-9]{12}"
                        >
                        <p class="mt-2 text-sm text-slate-500">
                            Format: 254712345678 (Kenya country code)
                        </p>
                    </div>

                    <div>
                        <label for="institution" class="block text-sm font-semibold text-slate-900 mb-2">
                            Institution/Organization
                        </label>
                        <input 
                            type="text" 
                            id="institution" 
                            name="institution" 
                            required
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--forest-green)] focus:border-transparent transition-all"
                            placeholder="University of Nairobi"
                        >
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-semibold text-slate-900 mb-2">
                            Additional Information (Optional)
                        </label>
                        <textarea 
                            id="message" 
                            name="message" 
                            rows="4"
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--forest-green)] focus:border-transparent transition-all"
                            placeholder="Tell us why you'd like to join..."
                        ></textarea>
                    </div>

                    <div id="formStatus" class="hidden"></div>

                    <button 
                        type="submit" 
                        class="w-full px-8 py-4 bg-gradient-to-r from-[var(--primary)] to-[var(--accent)] text-white rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Submit Request
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-200">
                    <p class="text-sm text-slate-600 text-center">
                        Already have an invitation? 
                        <a href="{{ url('/auth/register') }}" class="text-[var(--forest-green)] font-semibold hover:underline">
                            Complete your registration
                        </a>
                    </p>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="mt-8 grid sm:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-6 border border-slate-200 text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-900 mb-2">Secure & Private</h3>
                    <p class="text-sm text-slate-600">Your data is protected with enterprise-grade security</p>
                </div>

                <div class="bg-white rounded-lg p-6 border border-slate-200 text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-900 mb-2">Fast Review</h3>
                    <p class="text-sm text-slate-600">Most requests are approved within 24 hours</p>
                </div>

                <div class="bg-white rounded-lg p-6 border border-slate-200 text-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-900 mb-2">Free Forever</h3>
                    <p class="text-sm text-slate-600">No credit card required for basic features</p>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-12 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('brand/chamaconnect-logo.svg') }}" alt="ChamaConnect" class="w-10 h-10 rounded-lg"/>
                    <span class="text-lg font-bold text-white">ChamaConnect</span>
                </div>
                <div class="text-sm">
                    &copy; {{ date('Y') }} ChamaConnect. All rights reserved.
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('inviteForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(e.target);
            const statusDiv = document.getElementById('formStatus');
            const submitBtn = e.target.querySelector('button[type="submit"]');
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';
            
            try {
                const response = await fetch('/auth/invite', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({
                        email: formData.get('email')
                    })
                });
                
                if (response.ok) {
                    statusDiv.className = 'p-4 bg-green-50 border border-green-200 rounded-xl';
                    statusDiv.innerHTML = `
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-green-900">Request Submitted!</p>
                                <p class="text-sm text-green-700">Check your email for the invitation link.</p>
                            </div>
                        </div>
                    `;
                    e.target.reset();
                } else {
                    const data = await response.json();
                    statusDiv.className = 'p-4 bg-red-50 border border-red-200 rounded-xl';
                    statusDiv.innerHTML = `
                        <p class="font-semibold text-red-900">Error</p>
                        <p class="text-sm text-red-700">${data.message || 'Something went wrong. Please try again.'}</p>
                    `;
                }
            } catch (error) {
                statusDiv.className = 'p-4 bg-red-50 border border-red-200 rounded-xl';
                statusDiv.innerHTML = `
                    <p class="font-semibold text-red-900">Error</p>
                    <p class="text-sm text-red-700">${error.message}</p>
                `;
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Request';
            }
        });
    </script>
</body>
</html>

