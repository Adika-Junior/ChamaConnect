<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="TaskFlow Dashboard - Team Task Management">
    <title>Dashboard - TaskFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 antialiased">
    <!-- Navigation -->
    <header class="sticky top-0 z-50 glass-effect border-b border-slate-200 shadow-sm bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-[var(--forest-green)] to-[var(--brand-brown)] flex items-center justify-center text-white font-bold text-lg shadow-lg">
                        TT
                    </div>
                    <div>
                        <h1 class="text-lg font-bold bg-gradient-to-r from-[var(--forest-green)] to-[var(--brand-brown)] bg-clip-text text-transparent">TaskFlow</h1>
                        <p class="text-xs text-slate-500 hidden sm:block">Dashboard</p>
                    </div>
                </div>
                
                <nav class="flex items-center gap-4">
                    <div class="flex items-center gap-3 px-3 py-2 bg-slate-50 rounded-lg">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[var(--forest-green)] to-[var(--brand-brown)] flex items-center justify-center text-white text-sm font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="hidden sm:block">
                            <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 rounded-lg font-medium hover:bg-red-100 transition-all">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-[var(--forest-green)] to-[var(--brand-brown)] rounded-2xl shadow-xl p-8 mb-8">
            <div class="text-white">
                <h1 class="text-3xl sm:text-4xl font-bold mb-4">
                    Welcome back, {{ auth()->user()->name }}! 👋
                </h1>
                <p class="text-lg opacity-90">
                    Ready to manage your tasks and collaborate with your team
                </p>
            </div>
        </div>

        @include('dashboard-enhanced')

        <!-- User Info Card -->
        <div class="bg-white rounded-xl shadow-md border border-slate-200 p-6 mb-8">
            <h2 class="text-xl font-bold text-slate-900 mb-4">Your Account Information</h2>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-slate-600 mb-1">Name</p>
                    <p class="font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-600 mb-1">Email</p>
                    <p class="font-semibold text-slate-900">{{ auth()->user()->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-600 mb-1">Phone</p>
                    <p class="font-semibold text-slate-900">{{ auth()->user()->phone ?? 'Not provided' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-600 mb-1">Status</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                        {{ auth()->user()->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                        {{ auth()->user()->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ auth()->user()->status === 'inactive' ? 'bg-red-100 text-red-800' : '' }}
                        {{ auth()->user()->status === 'suspended' ? 'bg-gray-100 text-gray-800' : '' }}">
                        {{ ucfirst(auth()->user()->status) }}
                    </span>
                </div>
                @if(auth()->user()->employee_id)
                <div>
                    <p class="text-sm text-slate-600 mb-1">Employee ID</p>
                    <p class="font-semibold text-slate-900">{{ auth()->user()->employee_id }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-slate-600 mb-1">Account Created</p>
                    <p class="font-semibold text-slate-900">{{ auth()->user()->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-md border border-slate-200 p-6">
            <h2 class="text-xl font-bold text-slate-900 mb-4">Quick Actions</h2>
            <div class="grid md:grid-cols-3 gap-4">
                <button class="flex items-center gap-3 p-4 bg-slate-50 hover:bg-slate-100 rounded-lg transition-all text-left">
                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Create Task</p>
                        <p class="text-sm text-slate-600">Add new task to your list</p>
                    </div>
                </button>

                <button class="flex items-center gap-3 p-4 bg-slate-50 hover:bg-slate-100 rounded-lg transition-all text-left">
                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Schedule Meeting</p>
                        <p class="text-sm text-slate-600">Set up a new meeting</p>
                    </div>
                </button>

                <button class="flex items-center gap-3 p-4 bg-slate-50 hover:bg-slate-100 rounded-lg transition-all text-left">
                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-slate-900">Invite Team</p>
                        <p class="text-sm text-slate-600">Add team members</p>
                    </div>
                </button>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="mt-12 bg-white border-t border-slate-200 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-[var(--forest-green)] to-[var(--brand-brown)] flex items-center justify-center text-white font-bold">
                        TT
                    </div>
                    <span class="font-bold text-slate-900">TaskFlow</span>
                </div>
                <div class="text-sm text-slate-600">
                    &copy; {{ date('Y') }} TaskFlow. All rights reserved.
                </div>
            </div>
        </div>
    </footer>
</body>
</html>

