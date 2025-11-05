<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Documentation - ChamaConnect</title>
    <link rel="icon" type="image/svg+xml" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-50 via-white to-[var(--muted-cream)] text-slate-900 antialiased">
    <header class="sticky top-0 z-50 glass-effect">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('brand/chamaconnect-logo.svg') }}" alt="ChamaConnect" class="w-12 h-12 rounded-full border-2 border-white ring-2 ring-[var(--primary)]/20"/>
                    <div class="flex flex-col justify-center">
                        <h1 class="text-lg font-bold bg-gradient-to-r from-[var(--primary)] via-[var(--accent)] to-[var(--primary)] bg-clip-text text-transparent">ChamaConnect</h1>
                        <p class="text-xs text-slate-600 hidden sm:block font-medium">Documentation</p>
                    </div>
                </a>
                <nav class="flex items-center gap-3">
                    <a href="{{ url('/') }}" class="nav-link-modern">Home</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-3xl font-bold mb-4">Documentation</h2>
        <p class="text-slate-600 mb-6">This is a placeholder. Detailed docs will be added here.</p>
        <ul class="list-disc pl-6 text-slate-700 space-y-2">
            <li>Getting Started</li>
            <li>Managing SACCOs and Groups</li>
            <li>Fundraisers & Campaigns</li>
            <li>Meetings & Recordings</li>
        </ul>
    </main>
</body>
</html>

