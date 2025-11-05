<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Task - TaskFlow</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-5xl mx-auto py-10 px-4 space-y-8">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold">{{ $task->title }}</h1>
            <a href="{{ route('tasks.index') }}" class="text-sm text-slate-600 hover:underline">Back</a>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6">
            <p class="text-slate-700 whitespace-pre-line">{{ $task->description }}</p>
            <div class="mt-4 text-sm text-slate-600">Status: <strong>{{ $task->status }}</strong> • Priority: <strong>{{ $task->priority }}</strong></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="text-lg font-semibold mb-4">Assignees</h2>
                <form action="{{ route('tasks.assign', $task) }}" method="POST" class="space-y-3 mb-4">
                    @csrf
                    <input type="email" name="email" class="w-full px-3 py-2 rounded-lg border border-slate-300" placeholder="user@example.com">
                    <div>
                        <button class="px-3 py-2 bg-[var(--forest-green)] text-white rounded-md text-sm">Assign</button>
                    </div>
                </form>
                <ul class="space-y-2 text-sm">
                    @foreach($task->assignees as $assignee)
                        <li class="flex items-center justify-between">
                            <span>{{ $assignee->name }} ({{ $assignee->email }})</span>
                            <form action="{{ route('tasks.unassign', [$task, $assignee]) }}" method="POST">
                                @csrf
                                <button class="text-red-600 hover:underline">Remove</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="bg-white rounded-xl border border-slate-200 p-6 md:col-span-2">
                <h2 class="text-lg font-semibold mb-4">Comments</h2>
                <form action="{{ route('task-comments.store', $task) }}" method="POST" class="space-y-3 mb-4">
                    @csrf
                    <textarea name="body" rows="3" class="w-full px-3 py-2 rounded-lg border border-slate-300" placeholder="Write a comment..."></textarea>
                    <div>
                        <button class="px-3 py-2 bg-[var(--forest-green)] text-white rounded-md text-sm">Add Comment</button>
                    </div>
                </form>
                <div class="space-y-4">
                    @foreach($task->comments()->latest()->get() as $comment)
                        <div class="border border-slate-200 rounded-lg p-3">
                            <div class="text-sm text-slate-600">{{ $comment->user->name }} • {{ $comment->created_at->diffForHumans() }}</div>
                            <div class="mt-1 text-slate-800">{{ $comment->body }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 p-6">
                <h2 class="text-lg font-semibold mb-4">Attachments</h2>
                <form action="{{ route('task-attachments.store', $task) }}" method="POST" enctype="multipart/form-data" class="space-y-3 mb-4">
                    @csrf
                    <input type="file" name="file" class="text-sm" />
                    <div>
                        <button class="px-3 py-2 bg-[var(--forest-green)] text-white rounded-md text-sm">Upload</button>
                    </div>
                </form>
                <ul class="space-y-2 text-sm">
                    @foreach($task->attachments()->latest()->get() as $att)
                        <li>
                            <a href="{{ asset('storage/'.$att->path) }}" class="text-[var(--forest-green)] hover:underline" target="_blank">{{ $att->filename }}</a>
                            <span class="text-slate-500">({{ $att->mime_type }}, {{ number_format($att->size_bytes/1024,1) }} KB)</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</body>
</html>


