@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Contributions Export (CSV)</h1>
    <form method="POST" action="{{ route('admin.contributions.export.download') }}" class="space-y-4 bg-white p-4 rounded shadow">
        @csrf
        <div>
            <label class="block text-sm text-slate-600">From</label>
            <input type="date" name="from" class="border rounded px-3 py-2 w-full" required>
        </div>
        <div>
            <label class="block text-sm text-slate-600">To</label>
            <input type="date" name="to" class="border rounded px-3 py-2 w-full" required>
        </div>
        <div>
            <label class="block text-sm text-slate-600">Status (optional)</label>
            <select name="status" class="border rounded px-3 py-2 w-full">
                <option value="">Any</option>
                <option value="pending">Pending</option>
                <option value="paid">Paid</option>
                <option value="overdue">Overdue</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Download CSV</button>
    </form>
</div>
@endsection


