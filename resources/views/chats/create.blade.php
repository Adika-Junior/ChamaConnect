<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Start Chat - TaskFlow</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-xl mx-auto py-10 px-4">
        <h1 class="text-2xl font-bold mb-6">Start Chat</h1>
        <form action="{{ route('chats.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-2">Type</label>
                <select name="type" class="w-full px-4 py-3 rounded-xl border border-slate-300">
                    <option value="direct" selected>Direct</option>
                    <option value="group">Group</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">Group Name (for group)</label>
                <input name="name" class="w-full px-4 py-3 rounded-xl border border-slate-300" />
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">Participant Email (for direct)</label>
                <input type="email" name="participant_email" class="w-full px-4 py-3 rounded-xl border border-slate-300" />
            </div>
            <div class="pt-2">
                <button class="px-4 py-2 bg-[var(--forest-green)] text-white rounded-lg">Create</button>
                <a href="{{ route('chats.index') }}" class="ml-3 text-sm text-slate-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>


