@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Donation Export (CSV)</h1>
    <form method="POST" action="{{ route('admin.donations.export.download') }}" class="space-y-4 bg-white p-4 rounded shadow">
        @csrf
        <div>
            <label class="block text-sm text-slate-600">From</label>
            <input type="date" name="from" class="border rounded px-3 py-2 w-full" required>
        </div>
        <div>
            <label class="block text-sm text-slate-600">To</label>
            <input type="date" name="to" class="border rounded px-3 py-2 w-full" required>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" id="anonymize" name="anonymize" value="1" class="rounded">
            <label for="anonymize" class="text-sm">Anonymize donor details</label>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Download CSV</button>
    </form>
</div>
@endsection


