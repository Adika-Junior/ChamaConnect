@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Recurring Contribution Rules</h1>
        <a href="{{ route('admin.recurring_rules.create') }}" class="px-3 py-2 bg-blue-600 text-white rounded">New Rule</a>
    </div>

    @if (session('status'))
        <div class="bg-green-100 border border-green-200 text-green-800 p-3 rounded mb-4">{{ session('status') }}</div>
    @endif

    <div class="overflow-x-auto bg-white shadow rounded">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Recipient</th>
                    <th class="px-4 py-2 text-left">Amount</th>
                    <th class="px-4 py-2 text-left">Interval</th>
                    <th class="px-4 py-2 text-left">Next Run</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rules as $rule)
                <tr class="border-t">
                    <td class="px-4 py-2">#{{ $rule->id }}</td>
                    <td class="px-4 py-2">{{ $rule->recipient_name ?? '—' }}</td>
                    <td class="px-4 py-2">KES {{ number_format($rule->amount_cents / 100, 2) }}</td>
                    <td class="px-4 py-2">{{ ucfirst($rule->interval) }}</td>
                    <td class="px-4 py-2">{{ $rule->next_run_at }}</td>
                    <td class="px-4 py-2">{{ $rule->status }}</td>
                    <td class="px-4 py-2">
                        <a class="text-blue-600" href="{{ route('admin.recurring_rules.edit', $rule) }}">Edit</a>
                        <form method="POST" action="{{ route('admin.recurring_rules.destroy', $rule) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 ml-2" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $rules->links() }}
    </div>
</div>
@endsection


