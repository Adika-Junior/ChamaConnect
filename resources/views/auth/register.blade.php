<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="Complete your ChamaConnect registration">
    <title>Complete Registration - ChamaConnect</title>
    <link rel="icon" type="image/svg+xml" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
    <link rel="alternate icon" type="image/png" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
    <link rel="shortcut icon" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
    <meta name="theme-color" content="#0F766E">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                    <a href="{{ url('/login') }}" class="text-sm text-slate-700 hover:text-[var(--primary)] transition-colors font-medium">Sign In</a>
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
                    Complete Your Registration
                </h1>
                <p class="text-xl text-slate-600 mb-6">
                    Finish setting up your ChamaConnect account to access SACCO management, fundraising, meetings, and M-Pesa payments
                </p>
                <div id="tokenError" class="hidden bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <p class="font-semibold text-red-900">Invalid or Expired Invitation</p>
                            <p class="text-sm text-red-700">This invitation link is invalid or has expired. Please request a new invitation.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Form -->
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-slate-200">
                <form id="registerForm" method="POST" action="{{ $token ? url('/auth/register/'.$token) : '#' }}" class="space-y-6">
                    @csrf
                    <input type="hidden" id="token" name="token" value="{{ $token ?? '' }}">
                    @if(isset($email))
                        <div class="bg-teal-50 border border-teal-200 rounded-xl p-4 mb-6">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-teal-900">Registering for: {{ $email }}</p>
                                    <p class="text-sm text-teal-700">This email will be used for your account</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(isset($error))
                        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                            <div class="flex items-center gap-3">
                                <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold text-red-900">Error</p>
                                    <p class="text-sm text-red-700">{{ $error }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div id="formStatus" class="hidden"></div>

                    <div>
                        <label for="name" class="block text-sm font-semibold text-slate-900 mb-2">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            required
                            autofocus
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('name') border-red-500 @enderror"
                            placeholder="Enter your full name"
                            value="{{ old('name') }}"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-semibold text-slate-900 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="tel" 
                            id="phone" 
                            name="phone" 
                            required
                            pattern="[0-9]{12}"
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('phone') border-red-500 @enderror"
                            placeholder="254712345678"
                            value="{{ old('phone') }}"
                        >
                        <p class="mt-2 text-sm text-slate-500">Format: 254712345678 (Kenya country code)</p>
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-900 mb-2">
                            Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                minlength="12"
                                class="w-full px-4 py-3 pr-12 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('password') border-red-500 @enderror"
                                placeholder="Create a strong password"
                            >
                            <button 
                                type="button"
                                onclick="togglePassword('password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-700 transition-colors"
                            >
                                <svg id="passwordEyeIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        <p class="mt-2 text-sm text-slate-500">Minimum 12 characters with letters, numbers, and special characters</p>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-900 mb-2">
                            Confirm Password <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation" 
                                required
                                minlength="12"
                                class="w-full px-4 py-3 pr-12 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('password_confirmation') border-red-500 @enderror"
                                placeholder="Confirm your password"
                            >
                            <button 
                                type="button"
                                onclick="togglePassword('password_confirmation')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-500 hover:text-slate-700 transition-colors"
                            >
                                <svg id="passwordConfirmationEyeIcon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- User Type Selection -->
                    <div class="border-t border-slate-200 pt-6">
                        <label class="block text-sm font-semibold text-slate-900 mb-4">
                            What are you registering for? <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-start p-4 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-[var(--primary)] transition-all user-type-option">
                                <input 
                                    type="radio" 
                                    name="user_type" 
                                    value="sacco_member" 
                                    class="mt-1 w-5 h-5 text-[var(--primary)] focus:ring-[var(--primary)]"
                                    required
                                    {{ old('user_type') === 'sacco_member' ? 'checked' : '' }}
                                >
                                <div class="ml-3 flex-1">
                                    <div class="font-semibold text-slate-900">Join a SACCO</div>
                                    <div class="text-sm text-slate-600 mt-1">I want to become a member of an existing SACCO</div>
                                </div>
                            </label>
                            
                            <label class="flex items-start p-4 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-[var(--primary)] transition-all user-type-option">
                                <input 
                                    type="radio" 
                                    name="user_type" 
                                    value="chama" 
                                    class="mt-1 w-5 h-5 text-[var(--primary)] focus:ring-[var(--primary)]"
                                    required
                                    {{ old('user_type') === 'chama' ? 'checked' : '' }}
                                >
                                <div class="ml-3 flex-1">
                                    <div class="font-semibold text-slate-900">Create a Chama</div>
                                    <div class="text-sm text-slate-600 mt-1">I want to create and manage a new Chama group</div>
                                </div>
                            </label>
                            
                            <label class="flex items-start p-4 rounded-xl border-2 border-slate-200 cursor-pointer hover:border-[var(--primary)] transition-all user-type-option">
                                <input 
                                    type="radio" 
                                    name="user_type" 
                                    value="fundraiser" 
                                    class="mt-1 w-5 h-5 text-[var(--primary)] focus:ring-[var(--primary)]"
                                    required
                                    {{ old('user_type') === 'fundraiser' ? 'checked' : '' }}
                                >
                                <div class="ml-3 flex-1">
                                    <div class="font-semibold text-slate-900">Start a Fundraiser</div>
                                    <div class="text-sm text-slate-600 mt-1">I want to create a fundraising campaign</div>
                                </div>
                            </label>
                        </div>
                        @error('user_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- SACCO Selection (shown when user_type is sacco_member) -->
                    <div id="saccoSelectionSection" class="hidden border-t border-slate-200 pt-6">
                        <label for="group_id" class="block text-sm font-semibold text-slate-900 mb-2">
                            Select SACCO <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="group_id" 
                            name="group_id" 
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('group_id') border-red-500 @enderror"
                        >
                            <option value="">-- Select a SACCO --</option>
                            @foreach($availableSaccos ?? [] as $sacco)
                                <option value="{{ $sacco->id }}" {{ old('group_id') == $sacco->id ? 'selected' : '' }}>
                                    {{ $sacco->name }}@if($sacco->location) - {{ $sacco->location }}@endif
                                    @if($sacco->current_members) ({{ $sacco->current_members }} members)@endif
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-2 text-sm text-slate-500">Choose the SACCO you want to join. Your application will be reviewed by the SACCO administrators.</p>
                        @error('group_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Chama Creation Fields (shown when user_type is chama) -->
                    <div id="chamaSection" class="hidden border-t border-slate-200 pt-6 space-y-4">
                        <div>
                            <label for="chama_name" class="block text-sm font-semibold text-slate-900 mb-2">
                                Chama Name <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="chama_name" 
                                name="chama_name" 
                                value="{{ old('chama_name') }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('chama_name') border-red-500 @enderror"
                                placeholder="Enter your Chama name"
                            >
                            @error('chama_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="chama_description" class="block text-sm font-semibold text-slate-900 mb-2">
                                Description
                            </label>
                            <textarea 
                                id="chama_description" 
                                name="chama_description" 
                                rows="3"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('chama_description') border-red-500 @enderror"
                                placeholder="Describe your Chama's purpose and goals"
                            >{{ old('chama_description') }}</textarea>
                            @error('chama_description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="chama_location" class="block text-sm font-semibold text-slate-900 mb-2">
                                Location
                            </label>
                            <input 
                                type="text" 
                                id="chama_location" 
                                name="chama_location" 
                                value="{{ old('chama_location') }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('chama_location') border-red-500 @enderror"
                                placeholder="Enter location (e.g., Nairobi, Mombasa)"
                            >
                            @error('chama_location')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Fundraiser Creation Fields (shown when user_type is fundraiser) -->
                    <div id="fundraiserSection" class="hidden border-t border-slate-200 pt-6 space-y-4">
                        <div>
                            <label for="campaign_title" class="block text-sm font-semibold text-slate-900 mb-2">
                                Campaign Title <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                id="campaign_title" 
                                name="campaign_title" 
                                value="{{ old('campaign_title') }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('campaign_title') border-red-500 @enderror"
                                placeholder="Enter your fundraising campaign title"
                            >
                            @error('campaign_title')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="campaign_description" class="block text-sm font-semibold text-slate-900 mb-2">
                                Campaign Description <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="campaign_description" 
                                name="campaign_description" 
                                rows="4"
                                required
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('campaign_description') border-red-500 @enderror"
                                placeholder="Describe your fundraising campaign, its purpose, and how funds will be used"
                            >{{ old('campaign_description') }}</textarea>
                            @error('campaign_description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <div>
                            <label for="campaign_goal_amount" class="block text-sm font-semibold text-slate-900 mb-2">
                                Goal Amount (KES) <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                id="campaign_goal_amount" 
                                name="campaign_goal_amount" 
                                step="0.01"
                                min="0.01"
                                value="{{ old('campaign_goal_amount') }}"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--primary)] focus:border-transparent transition-all @error('campaign_goal_amount') border-red-500 @enderror"
                                placeholder="Enter target amount"
                            >
                            <p class="mt-2 text-sm text-slate-500">Enter the total amount you want to raise</p>
                            @error('campaign_goal_amount')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        class="w-full px-8 py-4 bg-gradient-to-r from-[var(--primary)] to-[var(--accent)] text-white rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Complete Registration
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-slate-200">
                    <p class="text-sm text-slate-600 text-center">
                        Already have an account? 
                        <a href="{{ url('/login') }}" class="text-[var(--primary)] font-semibold hover:underline">
                            Sign in
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
                        <p class="font-semibold mb-1">Secure Registration</p>
                        <p class="text-blue-700">Your information is encrypted and secure. After registration, your account will be reviewed by an administrator before activation.</p>
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
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + 'EyeIcon');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                `;
            } else {
                input.type = 'password';
                icon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                `;
            }
        }

        // Handle form submission - allow normal form submission for web, but show loading state
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const statusDiv = document.getElementById('formStatus');
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Registering...';
            
            // Show loading state
            statusDiv.className = 'p-4 bg-blue-50 border border-blue-200 rounded-xl';
            statusDiv.innerHTML = `
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-blue-600 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-blue-900">Processing Registration...</p>
                        <p class="text-sm text-blue-700">Please wait while we create your account.</p>
                    </div>
                </div>
            `;
            
            // Form will submit normally, no preventDefault
        });

        // Handle user type selection
        const userTypeOptions = document.querySelectorAll('input[name="user_type"]');
        const saccoSection = document.getElementById('saccoSelectionSection');
        const chamaSection = document.getElementById('chamaSection');
        const fundraiserSection = document.getElementById('fundraiserSection');

        function toggleUserTypeSections() {
            const selectedType = document.querySelector('input[name="user_type"]:checked')?.value;
            
            // Hide all sections
            saccoSection.classList.add('hidden');
            chamaSection.classList.add('hidden');
            fundraiserSection.classList.add('hidden');
            
            // Remove required attributes
            document.getElementById('group_id')?.removeAttribute('required');
            document.getElementById('chama_name')?.removeAttribute('required');
            document.getElementById('campaign_title')?.removeAttribute('required');
            document.getElementById('campaign_description')?.removeAttribute('required');
            document.getElementById('campaign_goal_amount')?.removeAttribute('required');
            
            // Show relevant section
            if (selectedType === 'sacco_member') {
                saccoSection.classList.remove('hidden');
                document.getElementById('group_id')?.setAttribute('required', 'required');
            } else if (selectedType === 'chama') {
                chamaSection.classList.remove('hidden');
                document.getElementById('chama_name')?.setAttribute('required', 'required');
            } else if (selectedType === 'fundraiser') {
                fundraiserSection.classList.remove('hidden');
                document.getElementById('campaign_title')?.setAttribute('required', 'required');
                document.getElementById('campaign_description')?.setAttribute('required', 'required');
                document.getElementById('campaign_goal_amount')?.setAttribute('required', 'required');
            }
        }

        userTypeOptions.forEach(option => {
            option.addEventListener('change', toggleUserTypeSections);
        });

        // Initial check for old input
        toggleUserTypeSections();

        // Check token validity on page load
        const token = document.getElementById('token').value;
        if (!token) {
            document.getElementById('tokenError').classList.remove('hidden');
            document.getElementById('registerForm').style.display = 'none';
        }
    </script>
</body>
</html>

