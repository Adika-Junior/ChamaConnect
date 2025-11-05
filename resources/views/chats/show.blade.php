<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Chat - TaskFlow</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-3xl mx-auto py-10 px-4 space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ $chat->name ?? 'Direct Chat' }}</h1>
            <a href="{{ route('chats.index') }}" class="text-sm text-slate-600 hover:underline">Back</a>
        </div>
        <div class="text-sm text-slate-600">Participants: {{ $chat->participants->pluck('name')->join(', ') }}</div>
        @if($chat->meeting)
            <div class="text-sm text-slate-600">Meeting: {{ $chat->meeting->title }} @if($chat->meeting->contribution) • <a class="underline" href="{{ route('contributions.show', $chat->meeting->contribution) }}">Contribution</a>@endif</div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-4">
            @foreach($chat->messages as $message)
                <div>
                    <div class="text-xs text-slate-500">{{ $message->sender->name }} • {{ $message->created_at->diffForHumans() }}</div>
                    <div class="text-slate-800">{{ $message->body }}</div>
                </div>
            @endforeach
            <div id="live-messages"></div>
        </div>

        @if($chat->meeting)
        <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-3">
            <div class="font-semibold">Meeting Recordings</div>
            @php($recs = \App\Models\MeetingRecording::where('meeting_id', $chat->meeting->id)->orderByDesc('created_at')->get())
            @if($recs->isEmpty())
                <div class="text-sm text-slate-500">No recordings yet.</div>
            @else
                <ul class="list-disc pl-5">
                @foreach($recs as $r)
                    <li>
                        <a class="text-[var(--forest-green)] underline" href="{{ Storage::disk('public')->url($r->file_path) }}" target="_blank">{{ $r->file_name }}</a>
                        <span class="text-xs text-slate-500 ml-2">{{ $r->created_at->format('Y-m-d H:i') }}</span>
                    </li>
                @endforeach
                </ul>
            @endif
        </div>
        @endif

        <form action="{{ route('messages.store', $chat) }}" method="POST" class="flex gap-2">
            @csrf
            <input name="body" class="flex-1 px-4 py-3 rounded-xl border border-slate-300" placeholder="Type a message" />
            <button class="px-4 py-2 bg-[var(--forest-green)] text-white rounded-lg">Send</button>
        </form>
    </div>
    <script>
        if (window.Echo) {
            window.Echo.private('chat.{{ $chat->id }}')
                .listen('MessageSent', (e) => {
                    const c = document.getElementById('live-messages');
                    const el = document.createElement('div');
                    el.innerHTML = `<div class="text-xs text-slate-500">${e.sender.name} • just now</div><div class="text-slate-800">${e.body}</div>`;
                    c.appendChild(el);
                });
        }
    </script>
</body>
</html>


