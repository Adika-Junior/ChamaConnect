@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Admin Activity Log</h1>
        <a class="px-3 py-2 bg-slate-700 text-white rounded" href="{{ route('admin.activities.export', request()->only('actor','action')) }}">Export CSV</a>
    </div>

    <form method="GET" class="grid grid-cols-3 gap-3 bg-white p-4 rounded shadow mb-4">
        <div>
            <label class="block text-sm text-slate-600">Actor ID</label>
            <input type="number" name="actor" value="{{ request('actor') }}" class="border rounded px-3 py-2 w-full">
        </div>
        <div>
            <label class="block text-sm text-slate-600">Action</label>
            <input type="text" name="action" value="{{ request('action') }}" class="border rounded px-3 py-2 w-full" placeholder="route name or keyword">
        </div>
        <div class="self-end">
            <button class="px-4 py-2 bg-blue-600 text-white rounded">Filter</button>
        </div>
    </form>

    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Actor</th>
                    <th class="px-4 py-2 text-left">Action</th>
                    <th class="px-4 py-2 text-left">Target</th>
                    <th class="px-4 py-2 text-left">IP / UA</th>
                    <th class="px-4 py-2 text-left">Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $a)
                <tr class="border-t">
                    <td class="px-4 py-2">#{{ $a->id }}</td>
                    <td class="px-4 py-2">{{ $a->actor_id }}</td>
                    <td class="px-4 py-2">{{ $a->action }}</td>
                    <td class="px-4 py-2">{{ $a->target_type }} #{{ $a->target_id }}</td>
                    <td class="px-4 py-2">{{ $a->ip }} / <span class="text-slate-500" title="{{ $a->user_agent }}">{{ Str::limit($a->user_agent, 24) }}</span></td>
                    <td class="px-4 py-2">{{ $a->created_at->toDayDateTimeString() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $activities->withQueryString()->links() }}</div>
</div>
@endsection


