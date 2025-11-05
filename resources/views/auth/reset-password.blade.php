<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Reset Password - ChamaConnect</title>
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
            </div>
        </div>
    </header>

    <main class="min-h-screen py-20">
        <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl sm:text-5xl font-bold text-slate-900 mb-4">
                    Set New Password
                </h1>
                <p class="text-xl text-slate-600">
                    Choose a strong password for your account
                </p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-200">
                <form action="{{ route('password.update') }}" method="POST" class="space-y-6" id="resetForm">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <input type="hidden" name="email" value="{{ $email }}">

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
                            Email
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            value="{{ $email }}"
                            disabled
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-slate-50 text-slate-600"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-900 mb-2">
                            New Password
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="w-full px-4 py-3 pr-12 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--forest-green)] focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                                placeholder="Enter new password"
                            >
                            <button 
                                type="button"
                                onclick="togglePassword('password', 'passwordIcon')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-700 transition-colors"
                            >
                                <svg id="passwordIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Minimum 8 characters, with letters, numbers, and symbols</p>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-900 mb-2">
                            Confirm New Password
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                required
                                class="w-full px-4 py-3 pr-12 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--forest-green)] focus:border-transparent transition-all"
                                placeholder="Confirm new password"
                            >
                            <button 
                                type="button"
                                onclick="togglePassword('password_confirmation', 'confirmPasswordIcon')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-700 transition-colors"
                            >
                                <svg id="confirmPasswordIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full px-6 py-4 bg-gradient-to-r from-[var(--forest-green)] to-[var(--brand-brown)] text-white rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Reset Password
                    </button>
                </form>
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

    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            
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

        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Resetting...';
        });
    </script>
</body>
</html>

