<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Sign in to ChamaConnect - SACCOs, Fundraisers, Meetings and M-Pesa">
    <title>Sign In - ChamaConnect</title>
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
                    <div>
                        <h1 class="text-xl font-bold bg-gradient-to-r from-[var(--primary)] via-[var(--accent)] to-[var(--primary)] bg-clip-text text-transparent">ChamaConnect</h1>
                        <p class="text-xs text-slate-600 hidden sm:block font-medium">SACCOs • Fundraising • Meetings • M‑Pesa</p>
                    </div>
                </a>
                <nav class="flex items-center gap-4">
                    <a href="{{ url('/invite') }}" class="text-sm text-slate-700 hover:text-[var(--forest-green)] transition-colors">Request Access</a>
                    <a href="{{ url('/') }}" class="inline-flex items-center px-4 py-2 bg-slate-100 text-slate-700 rounded-lg font-medium hover:bg-slate-200 transition-all">
                        Back to Home
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen py-20">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="text-center mb-12">
                <h1 class="text-4xl sm:text-5xl font-bold text-slate-900 mb-4">
                    Welcome Back
                </h1>
                <p class="text-xl text-slate-600 mb-6">
                    Sign in to your ChamaConnect account to access SACCO management, fundraising, meetings, and M-Pesa payments
                </p>
                <div class="flex flex-wrap justify-center gap-4 text-sm text-slate-500">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>Secure & Encrypted</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <span>M-Pesa Integrated</span>
                    </div>
                </div>
            </div>

            <!-- Login Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-200">
                <form action="{{ route('login.post') }}" method="POST" class="space-y-6" id="loginForm">
                    @csrf

                    <div id="formStatus" class="hidden"></div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-900 mb-2">
                            Email Address
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            autofocus
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--forest-green)] focus:border-transparent transition-all @error('email') border-red-500 @enderror"
                            placeholder="you@institution.ac.ke"
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-900 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="w-full px-4 py-3 pr-12 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--forest-green)] focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                                placeholder="Enter your password"
                            >
                            <button 
                                type="button"
                                onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-700 transition-colors"
                            >
                                <svg id="eyeIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                name="remember" 
                                class="w-4 h-4 rounded border-slate-300 text-[var(--forest-green)] focus:ring-[var(--forest-green)]"
                            >
                            <span class="ml-2 text-sm text-slate-600">Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm text-[var(--forest-green)] hover:underline font-medium">
                            Forgot password?
                        </a>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full px-8 py-4 bg-gradient-to-r from-[var(--primary)] to-[var(--accent)] text-white rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Sign In
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-200">
                    <p class="text-sm text-slate-600 text-center">
                        Don't have an account? 
                        <a href="{{ url('/invite') }}" class="text-[var(--forest-green)] font-semibold hover:underline">
                            Request access
                        </a>
                    </p>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <p class="font-semibold mb-1">Secure Login</p>
                        <p class="text-blue-700">Your connection is encrypted and secure. Password requirements: minimum 8 characters.</p>
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

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                `;
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                `;
            }
        }

        // Handle form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Signing in...';
        });
    </script>
</body>
</html>

