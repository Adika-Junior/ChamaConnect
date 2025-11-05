<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Your Profile - TaskFlow</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-3xl mx-auto py-10 px-4">
        <div class="mb-8">
            <h1 class="text-3xl font-bold">Your Profile</h1>
            <p class="text-slate-600">Manage your personal information</p>
        </div>

        @if(session('status'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                <ul class="text-sm text-red-800 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="flex items-center gap-6">
                    <div>
                        @if($user->avatar)
                            <img src="{{ asset('storage/'.$user->avatar) }}" alt="Avatar" class="w-20 h-20 rounded-full object-cover border" />
                        @else
                            <div class="w-20 h-20 rounded-full bg-slate-200 flex items-center justify-center text-slate-600">{{ substr($user->name,0,1) }}</div>
                        @endif
                    </div>
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold mb-2">Name</label>
                            <input name="name" type="text" value="{{ old('name',$user->name) }}" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--forest-green)] focus:border-transparent" required />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Email</label>
                            <input type="email" value="{{ $user->email }}" disabled class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-slate-50 text-slate-600" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Phone</label>
                            <input name="phone" type="text" value="{{ old('phone',$user->phone) }}" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-[var(--forest-green)] focus:border-transparent" />
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-2">Avatar</label>
                            <input name="avatar" type="file" accept="image/*" class="w-full text-sm" />
                        </div>
                    </div>
                </div>

                @if(auth()->user()->isAdmin())
                <div class="pt-4 border-t border-slate-200">
                    <h3 class="text-lg font-semibold mb-3">Security</h3>
                    <a href="{{ route('2fa.show') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700">
                        {{ auth()->user()->two_factor_enabled ? 'Manage Two-Factor Authentication' : 'Enable Two-Factor Authentication' }}
                    </a>
                </div>
                @endif

                <div class="pt-4">
                    <button type="submit" class="px-6 py-3 bg-[var(--forest-green)] text-white rounded-lg font-semibold hover:opacity-90">Save Changes</button>
                    <a href="{{ route('dashboard') }}" class="ml-3 text-sm text-slate-600 hover:underline">Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>


