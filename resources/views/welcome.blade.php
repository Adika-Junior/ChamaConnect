<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Enterprise-grade collaboration platform for Kenyan institutions, SACCOs, and corporate organizations. Manage tasks, conduct video meetings, process payments, and collaborate in real-time.">
    <title>ChamaConnect — SACCOs, Fundraisers, Meetings and M‑Pesa in one place</title>
        <link rel="icon" type="image/svg+xml" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
        <link rel="alternate icon" type="image/png" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
        <link rel="shortcut icon" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
    <meta name="theme-color" content="#0F766E">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root{
            --primary-deep: #0B3B2E; /* deep Kenyan green */
            --primary: #0F766E; /* teal */
            --accent: #F59E0B; /* amber/gold */
            --muted-cream: #F9FAF5; /* light background */
            --ink: #0F172A; /* slate-900 */
            --sky: #0EA5E9; /* sky */
            --forest-green: #0F766E; /* teal/primary */
            --brand-brown: #F59E0B; /* accent/gold */
            --accent-blue: #0EA5E9; /* sky blue */
        }
        .hero-gradient {
            background: radial-gradient(1200px 600px at 10% 10%, rgba(14,165,233,0.1), transparent 60%),
                        radial-gradient(1000px 500px at 90% 0%, rgba(245,158,11,0.15), transparent 60%),
                        linear-gradient(135deg, var(--primary-deep) 0%, var(--primary) 60%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px) saturate(1.3);
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.04), 0 4px 24px rgba(15, 118, 110, 0.08);
            border-bottom: 1px solid rgba(15, 118, 110, 0.1);
        }
        .modern-header {
            border-bottom: none;
        }
        .nav-link-modern {
            position: relative;
            padding: 0.75rem 1rem;
            font-weight: 600;
            font-size: 1rem;
            color: #0F172A;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 0.5rem;
        }
        .nav-link-modern:hover {
            background: rgba(15, 118, 110, 0.08);
            color: var(--primary);
        }
        .nav-link-modern::after {
            content: '';
            position: absolute;
            bottom: 0.25rem;
            left: 50%;
            transform: translateX(-50%) scaleX(0);
            width: 60%;
            height: 2px;
            background: var(--primary);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 2px;
        }
        .nav-link-modern:hover::after {
            transform: translateX(-50%) scaleX(1);
        }
        .btn-primary, .btn-secondary {
            min-height: 56px;
        }
        .dropdown-menu {
            animation: slideDown 0.2s ease-out;
        }
        /* Sign in dropdown visibility */
        .dropdown-panel { opacity: 0; visibility: hidden; transform: translateY(-6px); pointer-events: none; transition: opacity .15s ease, transform .15s ease, visibility .15s ease; }
        .group:hover .dropdown-panel,
        .group:focus-within .dropdown-panel { opacity: 1; visibility: visible; transform: translateY(0); pointer-events: auto; }
        /* Improve hero accent text visibility across displays */
        .text-outline { -webkit-text-stroke: 0.5px rgba(0,0,0,0.25); text-shadow: 0 2px 6px rgba(0,0,0,0.35); }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .feature-icon { transition: transform 0.3s ease; }
        .feature-card:hover .feature-icon { transform: translateY(-2px) scale(1.06); }
        .animated-bg { animation: gradient 18s ease infinite; background-size: 200% 200%; }
        @keyframes gradient { 0% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } 100% { background-position: 0% 50%; } }
        .badge-accent { background: linear-gradient(90deg, var(--accent), #FDBA74); color: #111827; }
        .ring-card { box-shadow: 0 10px 30px rgba(11,59,46,0.1); border: 1px solid rgba(15,118,110,0.08); }
        .cta-shadow { box-shadow: 0 12px 30px rgba(15,118,110,0.35); }
        .divider-wave { position: relative; overflow: hidden; }
        .divider-wave::after { content: ""; position: absolute; left: 0; right: 0; bottom: -1px; height: 60px; background:
            radial-gradient(50px 20px at 10% 0, rgba(15,118,110,0.08), transparent 70%),
            radial-gradient(50px 20px at 30% 0, rgba(245,158,11,0.08), transparent 70%),
            radial-gradient(50px 20px at 50% 0, rgba(14,165,233,0.08), transparent 70%),
            radial-gradient(50px 20px at 70% 0, rgba(15,118,110,0.08), transparent 70%),
            radial-gradient(50px 20px at 90% 0, rgba(245,158,11,0.08), transparent 70%);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-white to-[var(--muted-cream)] text-slate-900 antialiased">
    <!-- Navigation -->
    <header class="sticky top-0 z-50 glass-effect modern-header">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="relative">
                        <img src="{{ asset('brand/chamaconnect-logo.svg') }}" alt="ChamaConnect" class="w-14 h-14 rounded-full shadow-lg group-hover:shadow-xl transition-all border-2 border-white ring-2 ring-[var(--primary)]/20 group-hover:ring-[var(--primary)]/40"/>
                        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-[var(--primary)]/20 to-[var(--accent)]/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>
                <div class="flex flex-col justify-center">
                        <h1 class="text-xl font-bold bg-gradient-to-r from-[var(--primary)] via-[var(--accent)] to-[var(--primary)] bg-clip-text text-transparent">ChamaConnect</h1>
                        <p class="text-xs text-slate-600 hidden sm:block font-medium">SACCOs • Fundraising • Meetings • M‑Pesa</p>
                    </div>
            </a>

                <nav class="hidden md:flex items-center gap-3">
                    <a href="#features" class="nav-link-modern">Features</a>
                    <a href="#use-cases" class="nav-link-modern">Use Cases</a>
                    <a href="{{ url('/docs') }}" class="nav-link-modern">Docs</a>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.sacco-rules.index') }}" class="nav-link-modern">SACCO Rules</a>
                        @endif
                    @endauth
                    <a href="{{ route('sacco.register') }}" class="nav-link-modern" style="color: var(--primary);">Register a SACCO</a>
                    <div class="relative group ml-2">
                        <a href="{{ url('/login') }}" class="inline-flex items-center px-6 py-3 bg-white text-[var(--primary)] border border-[var(--primary)]/30 rounded-xl font-semibold text-base hover:bg-[var(--primary)]/5 transition-colors focus:outline-none focus:ring-2 focus:ring-[var(--primary)]/30">
                        Sign In
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </a>
                        <div class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl z-50 border border-slate-200 dropdown-menu dropdown-panel hidden group-hover:block group-focus-within:block">
                            <div class="py-1">
                                <a href="{{ url('/login') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    <div class="font-semibold">Sign In</div>
                                    <div class="text-xs text-slate-500">Existing account</div>
                                </a>
                                <a href="{{ url('/invite') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    <div class="font-semibold">Request Access</div>
                                    <div class="text-xs text-slate-500">Get invited to join</div>
                                </a>
                                <a href="{{ route('sacco.register') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                    <div class="font-semibold">Register SACCO</div>
                                    <div class="text-xs text-slate-500">New organization</div>
                                </a>
                            </div>
                        </div>
                    </div>
            </nav>

                <!-- Mobile menu button -->
                <button class="md:hidden p-2 rounded-lg text-slate-700 hover:bg-slate-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden py-20 sm:py-32 hero-gradient divider-wave">
        <div class="absolute inset-0 -z-10" style="background: radial-gradient(800px 400px at 20% 80%, rgba(255,255,255,0.08), transparent 60%);"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white rounded-full shadow-md mb-6">
                        <span class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                        <span class="text-sm font-medium text-slate-700">Production Ready</span>
                    </div>
                    
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight">
                        <span class="block text-white">All‑in‑one for</span>
                        <span class="block bg-gradient-to-r from-amber-200 via-yellow-100 to-sky-200 bg-clip-text text-transparent">SACCOs & Fundraisers</span>
                        <span class="block text-white">in Kenya</span>
                    </h1>
                    
                    <p class="mt-6 text-xl text-white leading-relaxed font-semibold">
                        ChamaConnect unifies SACCO management, transparent fundraising, Janus‑powered meetings, real‑time chat, and M‑Pesa payments—purpose‑built for Kenyan communities, families, and organizations.
                    </p>

                    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                        <a href="{{ route('sacco.register') }}" class="btn-primary inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-[var(--primary)] to-[var(--accent)] text-white rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                            Register a SACCO
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                        <a href="#features" class="btn-secondary inline-flex items-center justify-center px-8 py-4 bg-white text-slate-900 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all border-2 border-slate-200 hover:border-[var(--primary)] transform hover:scale-105">
                            Explore Features
                        </a>
                        <a href="{{ route('campaigns.index') }}" class="btn-secondary inline-flex items-center justify-center px-8 py-4 bg-white text-slate-900 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all border-2 border-slate-200 hover:border-[var(--primary)] transform hover:scale-105">
                            Start a Fundraiser
                        </a>
                    </div>

                    <div class="mt-8 flex items-center justify-center lg:justify-start gap-8 text-sm text-white/90">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">No Credit Card</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="font-semibold">14-Day Free Trial</span>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Preview -->
                <div class="relative mt-10 lg:mt-0">
                    <div class="relative bg-white rounded-2xl shadow-2xl p-4 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-[var(--forest-green)]/10 to-transparent"></div>
                        <div class="relative bg-slate-50 rounded-xl p-6 border border-slate-200">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-3 h-3 rounded-full bg-red-400"></div>
                                <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                                <div class="w-3 h-3 rounded-full bg-green-400"></div>
                                <div class="ml-auto text-sm text-slate-500">Dashboard Preview</div>
                            </div>
                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <div class="bg-white rounded-lg p-4 border border-slate-200">
                                    <div class="text-2xl font-bold text-[var(--forest-green)]">142</div>
                                    <div class="text-sm text-slate-600">Active Tasks</div>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-slate-200">
                                    <div class="text-2xl font-bold text-blue-600">24</div>
                                    <div class="text-sm text-slate-600">Team Members</div>
                                </div>
                                <div class="bg-white rounded-lg p-4 border border-slate-200">
                                    <div class="text-2xl font-bold text-green-600">98%</div>
                                    <div class="text-sm text-slate-600">Completion</div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="bg-white rounded-lg p-4 border-l-4 border-green-500">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">Q4 Marketing Campaign</span>
                                        <span class="text-sm text-green-600">Complete</span>
                                    </div>
                                </div>
                                <div class="bg-white rounded-lg p-4 border-l-4 border-blue-500">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">Product Launch Prep</span>
                                        <span class="text-sm text-blue-600">In Progress</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Floating badges -->
                    <div class="absolute -top-4 -right-4 bg-white rounded-xl shadow-lg p-3 animate-bounce">
                        <div class="flex items-center gap-2 text-sm">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <span class="font-medium">Live Updates</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- What's New Section -->
    <section class="py-20 bg-gradient-to-br from-slate-50 to-white border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-8 items-start">
                <div>
                    <h2 class="text-3xl font-bold text-slate-900 mb-3">What's New</h2>
                    <p class="text-slate-600 mb-6">Latest updates rolled out to enhance reliability and workflows.</p>
                    <ul class="space-y-3 text-slate-700">
                        <li>✅ Pledge reminders (SMS/email) and automatic overdue tracking</li>
                        <li>✅ Auto‑transcription pipeline for meeting recordings</li>
                        <li>✅ Breakout rooms (beta) and virtual background toggle</li>
                        <li>✅ Recurring contributions that auto‑create pledges</li>
                        <li>✅ SACCO rule‑based contributions with admin‑managed rules</li>
                    </ul>
                </div>
                <div class="bg-white rounded-2xl shadow-lg p-6 border border-slate-200">
                    <h3 class="text-xl font-semibold mb-4">Quick Links</h3>
                    <div class="grid sm:grid-cols-2 gap-3">
                        <a class="btn btn-light btn-block border border-slate-200 rounded-lg p-3 text-center" href="{{ route('meetings.index') }}">Meetings</a>
                        <a class="btn btn-light btn-block border border-slate-200 rounded-lg p-3 text-center" href="{{ route('contributions.index') }}">Contributions</a>
                        <a class="btn btn-light btn-block border border-slate-200 rounded-lg p-3 text-center" href="{{ route('campaigns.index') }}">Campaigns</a>
                        <a class="btn btn-light btn-block border border-slate-200 rounded-lg p-3 text-center" href="{{ route('notifications.index') }}">Notifications</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-slate-900 mb-4">
                    Everything Your Team Needs to Succeed
                </h2>
                <p class="text-xl text-slate-600 max-w-3xl mx-auto">
                    Powerful features designed for modern teams and organizations
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-gradient-to-br from-white to-slate-50 rounded-2xl p-8 border border-slate-200 hover:border-[var(--forest-green)] transition-all hover:shadow-xl">
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-6 feature-icon">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">SACCO & Group Management</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Manage members, apply rule‑based contributions, track expenses, and maintain transparent ledgers tailored for SACCOs and groups.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-gradient-to-br from-white to-slate-50 rounded-2xl p-8 border border-slate-200 hover:border-[var(--forest-green)] transition-all hover:shadow-xl">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center mb-6 feature-icon">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Meetings & Collaboration</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Janus‑powered HD meetings with screen share, breakout rooms (beta), auto‑recording, transcription, and in‑meeting chat.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-gradient-to-br from-white to-slate-50 rounded-2xl p-8 border border-slate-200 hover:border-[var(--forest-green)] transition-all hover:shadow-xl">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-6 feature-icon">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Real‑Time Chat</h3>
                    <p class="text-slate-600 leading-relaxed">
                        WhatsApp-style messaging with group chats, file sharing, and read receipts. Stay connected with your team instantly.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card bg-gradient-to-br from-white to-slate-50 rounded-2xl p-8 border border-slate-200 hover:border-[var(--forest-green)] transition-all hover:shadow-xl">
                    <div class="w-14 h-14 bg-gradient-to-br from-[var(--brand-brown)] to-[var(--forest-green)] rounded-xl flex items-center justify-center mb-6 feature-icon">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">M‑Pesa Integration</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Process payments and collect contributions directly through Safaricom M-Pesa. Secure transaction handling for SACCOs and groups.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card bg-gradient-to-br from-white to-slate-50 rounded-2xl p-8 border border-slate-200 hover:border-[var(--forest-green)] transition-all hover:shadow-xl">
                    <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-orange-500 rounded-xl flex items-center justify-center mb-6 feature-icon">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Fundraising & Campaigns</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Launch transparent campaigns for weddings, funerals, medical needs, and community projects—pledges, donor wall, and expense tracking.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card bg-gradient-to-br from-white to-slate-50 rounded-2xl p-8 border border-slate-200 hover:border-[var(--forest-green)] transition-all hover:shadow-xl">
                    <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-pink-500 rounded-xl flex items-center justify-center mb-6 feature-icon">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Automation & Reminders</h3>
                    <p class="text-slate-600 leading-relaxed">
                        Recurring contributions, pledge reminders (SMS/email), auto‑mark fulfillment on payment, and admin‑managed SACCO rules.
                    </p>
                </div>
            </div>
        </div>
        </section>

    <!-- Use Cases Section -->
    <section id="use-cases" class="py-24 bg-gradient-to-br from-slate-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-slate-900 mb-4">
                    Built for Your Organization
                </h2>
                <p class="text-xl text-slate-600">Perfect for institutions, SACCOs, and corporate teams</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-8 shadow-lg border border-slate-200">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Corporate Teams</h3>
                    <p class="text-slate-600 mb-4">Manage projects, track deadlines, and conduct meetings with integrated video conferencing and task boards.</p>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Department-based access control
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Gantt chart timeline view
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Automated reporting & analytics
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-lg border border-slate-200">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">SACCOs & Groups</h3>
                    <p class="text-slate-600 mb-4">Manage member contributions, track group finances, and process payments with integrated M-Pesa.</p>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            M-Pesa payment integration
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Financial transparency
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Automated contribution tracking
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-lg border border-slate-200">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center mb-6">
                        <svg class="w-7 h-7 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3">Educational Institutions</h3>
                    <p class="text-slate-600 mb-4">Coordinate campus projects, manage departments, and facilitate collaboration between students and staff.</p>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Role-based permissions
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Department management
                        </li>
                        <li class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-green-500 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            Secure admin controls
                        </li>
                    </ul>
                </div>
                </div>
            </div>
        </section>

    <!-- CTA Section -->
    <section class="py-24 hero-gradient text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl lg:text-5xl font-bold mb-6">
                Ready to power your SACCO or fundraiser?
            </h2>
            <p class="text-xl text-white/90 max-w-3xl mx-auto mb-10">
                Join Kenyan SACCOs, families, and organizations using ChamaConnect for contributions, meetings, and transparent fundraising.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('sacco.register') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-[var(--forest-green)] rounded-xl font-bold text-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-105">
                    Register a SACCO
                    <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
                <a href="{{ url('/docs') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white/10 text-white border-2 border-white/30 rounded-xl font-bold text-lg backdrop-blur-sm hover:bg-white/20 transition-all">
                    View Documentation
                </a>
            </div>
            <p class="mt-8 text-sm text-white/70">
                No credit card required • 14-day free trial • Full access to all features
            </p>
            </div>
        </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8 mb-12">
                <div class="md:col-span-2">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('brand/chamaconnect-logo.svg') }}" alt="ChamaConnect" class="w-10 h-10 rounded-lg"/>
                        <span class="text-lg font-bold text-white">ChamaConnect</span>
                    </div>
                    <p class="text-slate-400 max-w-md">
                        SACCO‑first collaboration and fundraising platform for Kenyan communities, families, and organizations.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Product</h4>
                    <ul class="space-y-2">
                        <li><a href="#features" class="hover:text-white transition-colors">Features</a></li>
                        <li><a href="#use-cases" class="hover:text-white transition-colors">Use Cases</a></li>
                        <li><a href="{{ url('/docs') }}" class="hover:text-white transition-colors">Documentation</a></li>
                        <li><a href="{{ route('sacco.register') }}" class="hover:text-white transition-colors">Get Started</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-4">Company</h4>
                    <ul class="space-y-2">
                        <li><a href="/about" class="hover:text-white transition-colors">About</a></li>
                        <li><a href="/contact" class="hover:text-white transition-colors">Contact</a></li>
                        <li><a href="/privacy" class="hover:text-white transition-colors">Privacy</a></li>
                        <li><a href="/terms" class="hover:text-white transition-colors">Terms</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-slate-800 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-slate-500">&copy; {{ date('Y') }} ChamaConnect. All rights reserved.</p>
                <div class="flex gap-6 mt-4 md:mt-0">
                    <a href="#" class="hover:text-white transition-colors">
                        <span class="sr-only">Twitter</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                        </svg>
                    </a>
                    <a href="#" class="hover:text-white transition-colors">
                        <span class="sr-only">GitHub</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>
    
</body>
</html>
