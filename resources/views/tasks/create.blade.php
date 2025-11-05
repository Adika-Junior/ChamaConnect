<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Create Task - TaskFlow</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-2xl mx-auto py-10 px-4">
        <h1 class="text-2xl font-bold mb-6">Create Task</h1>
        <form action="{{ route('tasks.store') }}" method="POST" class="space-y-6">
            @csrf
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
                <label class="block text-sm font-semibold mb-2">Title</label>
                <input name="title" class="w-full px-4 py-3 rounded-xl border border-slate-300" required />
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">Description</label>
                <textarea name="description" rows="5" class="w-full px-4 py-3 rounded-xl border border-slate-300"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">Priority</label>
                    <select name="priority" class="w-full px-4 py-3 rounded-xl border border-slate-300">
                        <option>low</option>
                        <option selected>medium</option>
                        <option>high</option>
                        <option>urgent</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Due date</label>
                    <input type="datetime-local" name="due_at" class="w-full px-4 py-3 rounded-xl border border-slate-300" />
                </div>
            </div>
            <div class="pt-2">
                <button class="px-4 py-2 bg-[var(--forest-green)] text-white rounded-lg">Create</button>
                <a href="{{ route('tasks.index') }}" class="ml-3 text-sm text-slate-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>


