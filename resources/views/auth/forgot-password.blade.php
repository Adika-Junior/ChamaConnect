<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Forgot Password - ChamaConnect</title>
    <link rel="icon" type="image/svg+xml" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
    <link rel="alternate icon" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
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
    <header class="sticky top-0 z-50 glass-effect border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('brand/chamaconnect-logo.svg') }}" alt="ChamaConnect" class="w-12 h-12 rounded-full shadow-md group-hover:shadow-lg transition-all border border-slate-200/50"/>
                    <div>
                        <h1 class="text-lg font-bold bg-gradient-to-r from-[var(--primary)] to-[var(--accent)] bg-clip-text text-transparent">ChamaConnect</h1>
                        <p class="text-xs text-slate-500 hidden sm:block">SACCOs • Fundraising • Meetings • M‑Pesa</p>
                    </div>
                </a>
                <nav class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="text-sm text-slate-700 hover:text-[var(--forest-green)] transition-colors">
                        Back to Sign In
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <main class="min-h-screen py-20">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl sm:text-5xl font-bold text-slate-900 mb-4">
                    Reset Password
                </h1>
                <p class="text-xl text-slate-600">
                    Enter your email to receive a reset link
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-200">
                <form action="{{ route('password.email') }}" method="POST" class="space-y-6">
                    @csrf

                    @if(session('status'))
                        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                            <ul class="text-sm text-red-800 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

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

                    <button 
                        type="submit" 
                        class="w-full px-6 py-4 bg-gradient-to-r from-[var(--forest-green)] to-[var(--brand-brown)] text-white rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-[1.02]"
                    >
                        Send Reset Link
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-200">
                    <p class="text-sm text-slate-600 text-center">
                        Remember your password? 
                        <a href="{{ route('login') }}" class="text-[var(--forest-green)] font-semibold hover:underline">
                            Sign in
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </main>

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
</body>
</html>

