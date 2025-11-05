<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tasks - TaskFlow</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-7xl mx-auto py-10 px-4">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">Tasks</h1>
            <a href="{{ route('tasks.create') }}" class="px-4 py-2 bg-[var(--forest-green)] text-white rounded-lg">New Task</a>
        </div>

        @if(session('status'))
            <div class="mb-4 bg-green-50 border border-green-200 rounded-xl p-3 text-sm text-green-800">{{ session('status') }}</div>
        @endif

        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Priority</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @foreach($tasks as $task)
                        <tr>
                            <td class="px-6 py-4 text-sm">{{ $task->title }}</td>
                            <td class="px-6 py-4 text-sm">{{ $task->status }}</td>
                            <td class="px-6 py-4 text-sm">{{ $task->priority }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('tasks.show', $task) }}" class="text-[var(--forest-green)] hover:underline text-sm">View</a>
                                <a href="{{ route('tasks.edit', $task) }}" class="text-slate-700 hover:underline text-sm">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $tasks->links() }}</div>
    </div>
</body>
</html>


