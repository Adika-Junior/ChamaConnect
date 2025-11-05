<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Register your SACCO with ChamaConnect">
    <title>Register Your SACCO - ChamaConnect</title>
    <link rel="icon" type="image/svg+xml" href="{{ url('brand/chamaconnect-logo.svg') }}?v=2">
    <link rel="alternate icon" type="image/png" href="{{ url('brand/chamaconnect-logo.svg') }}?v=2">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('brand/chamaconnect-logo.svg') }}?v=2">
    <link rel="shortcut icon" href="{{ url('brand/chamaconnect-logo.svg') }}?v=2">
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
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px) saturate(1.3);
            box-shadow: 0 1px 0 rgba(0, 0, 0, 0.04), 0 4px 24px rgba(15, 118, 110, 0.08);
            border-bottom: 1px solid rgba(15, 118, 110, 0.1);
        }
        .form-section {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            margin-bottom: 1.5rem;
        }
        .form-section-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid rgba(15, 118, 110, 0.1);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-white to-[var(--muted-cream)] text-slate-900 antialiased">
    <!-- Navigation -->
    <header class="sticky top-0 z-50 glass-effect">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('brand/chamaconnect-logo.svg') }}" alt="ChamaConnect" class="w-14 h-14 rounded-full shadow-lg group-hover:shadow-xl transition-all border-2 border-white ring-2 ring-[var(--primary)]/20 group-hover:ring-[var(--primary)]/40"/>
                    <div>
                        <h1 class="text-xl font-bold bg-gradient-to-r from-[var(--primary)] via-[var(--accent)] to-[var(--primary)] bg-clip-text text-transparent">ChamaConnect</h1>
                        <p class="text-xs text-slate-600 hidden sm:block font-medium">SACCOs • Fundraising • Meetings • M‑Pesa</p>
                    </div>
                </a>
                <nav class="flex items-center gap-4">
                    <a href="{{ url('/login') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[var(--primary)] to-[var(--accent)] text-white rounded-lg font-semibold shadow hover:shadow-md transition-all">
                        Sign In
                    </a>
                    <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-700 rounded-lg font-medium hover:bg-slate-200 transition-all">
                        Back to Home
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="text-center mb-12">
                <h1 class="text-4xl sm:text-5xl font-bold text-slate-900 mb-4">
                    Register Your SACCO
                </h1>
                <p class="text-xl text-slate-600 mb-6">
                    Join ChamaConnect to manage your SACCO with transparent financial tracking, M-Pesa integration, and collaborative tools
                </p>
                @if(session('status'))
                    <div class="max-w-2xl mx-auto bg-green-50 border border-green-200 rounded-xl p-4 mb-6">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-green-900 font-medium">{{ session('status') }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Registration Form -->
            <form method="POST" action="{{ route('sacco.register.store') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- SACCO Information -->
                <div class="form-section">
                    <h2 class="form-section-title">SACCO Information</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-slate-900 mb-2">
                                SACCO Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="name" 
                                name="name" 
                                required
                                value="{{ old('name') }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('name') border-red-500 @enderror"
                                placeholder="Enter SACCO name"
                            >
                            @error('name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="registration_number" class="block text-sm font-semibold text-slate-900 mb-2">
                                Registration Number <span class="text-red-500">*</span>
                                <span class="badge bg-info ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Official registration number from the registrar of societies or relevant authority.">ℹ️</span>
                            </label>
                            <input 
                                type="text" 
                                id="registration_number" 
                                name="registration_number" 
                                required
                                value="{{ old('registration_number') }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('registration_number') border-red-500 @enderror"
                                placeholder="Enter registration number"
                            >
                            @error('registration_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="registered_at" class="block text-sm font-semibold text-slate-900 mb-2">
                                Registration Date
                            </label>
                            <input 
                                type="date" 
                                id="registered_at" 
                                name="registered_at" 
                                value="{{ old('registered_at') }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('registered_at') border-red-500 @enderror"
                            >
                            @error('registered_at')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="county" class="block text-sm font-semibold text-slate-900 mb-2">
                                County
                            </label>
                            <input 
                                type="text" 
                                id="county" 
                                name="county" 
                                value="{{ old('county') }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('county') border-red-500 @enderror"
                                placeholder="Enter county"
                            >
                            @error('county')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-semibold text-slate-900 mb-2">
                                Address
                            </label>
                            <input 
                                type="text" 
                                id="address" 
                                name="address" 
                                value="{{ old('address') }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('address') border-red-500 @enderror"
                                placeholder="Enter physical address"
                            >
                            @error('address')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="form-section">
                    <h2 class="form-section-title">Contact Information</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="contact_email" class="block text-sm font-semibold text-slate-900 mb-2">
                                Contact Email <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="email" 
                                id="contact_email" 
                                name="contact_email" 
                                required
                                value="{{ old('contact_email') }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('contact_email') border-red-500 @enderror"
                                placeholder="contact@sacco.co.ke"
                            >
                            @error('contact_email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_phone" class="block text-sm font-semibold text-slate-900 mb-2">
                                Contact Phone <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="tel" 
                                id="contact_phone" 
                                name="contact_phone" 
                                required
                                value="{{ old('contact_phone') }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('contact_phone') border-red-500 @enderror"
                                placeholder="254712345678"
                            >
                            <p class="mt-2 text-sm text-slate-500">Format: 254712345678 (Kenya country code)</p>
                            @error('contact_phone')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div class="form-section">
                    <h2 class="form-section-title">Supporting Documents</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label for="certificate" class="block text-sm font-semibold text-slate-900 mb-2">
                                Certificate of Registration
                                <span class="badge bg-info ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Upload a scanned copy of your official registration certificate. This helps us verify your SACCO's legitimacy.">ℹ️</span>
                            </label>
                            <input 
                                type="file" 
                                id="certificate" 
                                name="certificate" 
                                accept="application/pdf,image/*"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('certificate') border-red-500 @enderror"
                            >
                            <p class="mt-2 text-sm text-slate-500">PDF or image file (max 10MB)</p>
                            @error('certificate')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bylaws" class="block text-sm font-semibold text-slate-900 mb-2">
                                By-laws/Constitution
                            </label>
                            <input 
                                type="file" 
                                id="bylaws" 
                                name="bylaws" 
                                accept="application/pdf,image/*"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('bylaws') border-red-500 @enderror"
                            >
                            <p class="mt-2 text-sm text-slate-500">PDF or image file (max 10MB)</p>
                            @error('bylaws')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Officials Information -->
                <div class="form-section">
                    <h2 class="form-section-title">Key Officials</h2>
                    <div class="space-y-6">
                        <!-- Chairperson -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="chair_name" class="block text-sm font-semibold text-slate-900 mb-2">
                                    Chairperson Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="chair_name" 
                                    name="chair_name" 
                                    required
                                    value="{{ old('chair_name') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('chair_name') border-red-500 @enderror"
                                    placeholder="Enter chairperson name"
                                >
                                @error('chair_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="chair_phone" class="block text-sm font-semibold text-slate-900 mb-2">
                                    Chairperson Phone <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    id="chair_phone" 
                                    name="chair_phone" 
                                    required
                                    value="{{ old('chair_phone') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('chair_phone') border-red-500 @enderror"
                                    placeholder="254712345678"
                                >
                                @error('chair_phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Secretary -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="secretary_name" class="block text-sm font-semibold text-slate-900 mb-2">
                                    Secretary Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="secretary_name" 
                                    name="secretary_name" 
                                    required
                                    value="{{ old('secretary_name') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('secretary_name') border-red-500 @enderror"
                                    placeholder="Enter secretary name"
                                >
                                @error('secretary_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="secretary_phone" class="block text-sm font-semibold text-slate-900 mb-2">
                                    Secretary Phone <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    id="secretary_phone" 
                                    name="secretary_phone" 
                                    required
                                    value="{{ old('secretary_phone') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('secretary_phone') border-red-500 @enderror"
                                    placeholder="254712345678"
                                >
                                @error('secretary_phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Treasurer -->
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="treasurer_name" class="block text-sm font-semibold text-slate-900 mb-2">
                                    Treasurer Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="treasurer_name" 
                                    name="treasurer_name" 
                                    required
                                    value="{{ old('treasurer_name') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('treasurer_name') border-red-500 @enderror"
                                    placeholder="Enter treasurer name"
                                >
                                @error('treasurer_name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="treasurer_phone" class="block text-sm font-semibold text-slate-900 mb-2">
                                    Treasurer Phone <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="tel" 
                                    id="treasurer_phone" 
                                    name="treasurer_phone" 
                                    required
                                    value="{{ old('treasurer_phone') }}"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('treasurer_phone') border-red-500 @enderror"
                                    placeholder="254712345678"
                                >
                                @error('treasurer_phone')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex flex-col sm:flex-row gap-4 justify-end">
                    <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-8 py-4 bg-white text-slate-700 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all border-2 border-slate-200 hover:border-slate-300 transform hover:scale-105">
                        Cancel
                    </a>
                    <button 
                        type="submit" 
                        class="relative z-10 inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-[var(--primary)] to-[var(--accent)] text-white rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-105 border-2 border-[var(--primary)]/60"
                    >
                        Submit Registration
                        <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Info Notice -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Registration Review Process</p>
                        <p class="text-blue-700">Your registration will be reviewed by our administrators. You will be notified via email once your SACCO is approved and activated on the platform.</p>
                    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
      });
    });
    </script>
</body>
</html>
