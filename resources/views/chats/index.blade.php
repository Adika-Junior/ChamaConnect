<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Chats - TaskFlow</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    </head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto py-10 px-4">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Chats</h1>
            <a href="{{ route('chats.create') }}" class="px-4 py-2 bg-[var(--forest-green)] text-white rounded-lg">New Chat</a>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 divide-y">
            @forelse($chats as $chat)
                <a href="{{ route('chats.show', $chat) }}" class="block px-6 py-4 hover:bg-slate-50">
                    <div class="font-semibold">{{ $chat->name ?? 'Direct Chat' }}</div>
                    <div class="text-sm text-slate-600">Participants: {{ $chat->participants->pluck('name')->join(', ') }}</div>
                </a>
            @empty
                <div class="px-6 py-8 text-center text-slate-600">No chats yet</div>
            @endforelse
        </div>
    </div>
</body>
</html>


