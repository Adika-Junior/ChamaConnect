@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Payments Webhooks</h1>

    @if (session('status'))
        <div class="bg-green-100 border border-green-200 text-green-800 p-3 rounded mb-4">{{ session('status') }}</div>
    @endif

    <div class="flex items-center gap-3 mb-4">
        <a href="{{ route('admin.payments.webhooks.index') }}" class="text-blue-600">All</a>
        <a href="{{ route('admin.payments.webhooks.index', ['status' => 'failed']) }}" class="text-red-600">Failed</a>
        <a href="{{ route('admin.payments.webhooks.index', ['status' => 'received']) }}" class="text-slate-600">Received</a>
        <a href="{{ route('admin.payments.webhooks.index', ['status' => 'processed']) }}" class="text-green-600">Processed</a>
    </div>

    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Provider</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Processed At</th>
                    <th class="px-4 py-2 text-left">Error</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($events as $event)
                <tr class="border-t">
                    <td class="px-4 py-2">#{{ $event->id }}</td>
                    <td class="px-4 py-2">{{ $event->provider }}</td>
                    <td class="px-4 py-2">{{ $event->status }}</td>
                    <td class="px-4 py-2">{{ $event->processed_at }}</td>
                    <td class="px-4 py-2 w-1/2 truncate" title="{{ $event->error }}">{{ $event->error }}</td>
                    <td class="px-4 py-2">
                        <form method="POST" action="{{ route('admin.payments.webhooks.retry', $event) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded disabled:opacity-50" @if($event->status==='processed') disabled @endif>Retry</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $events->withQueryString()->links() }}
    </div>
</div>
@endsection


