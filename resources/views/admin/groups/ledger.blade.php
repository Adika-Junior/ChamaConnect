@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
  <h1 class="text-2xl font-semibold mb-4">Ledger Export - {{ $group->name }}</h1>
  <form method="GET" action="{{ route('admin.groups.ledger.export', $group) }}" class="space-y-4 bg-white p-4 rounded shadow">
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm text-slate-600">From</label>
        <input type="date" name="from" class="border rounded px-3 py-2 w-full" required>
      </div>
      <div>
        <label class="block text-sm text-slate-600">To</label>
        <input type="date" name="to" class="border rounded px-3 py-2 w-full" required>
      </div>
    </div>
    <button class="px-4 py-2 bg-blue-600 text-white rounded" type="submit">Download CSV</button>
  </form>
  <div class="mt-6">
    <form method="GET" action="{{ route('admin.groups.statement', $group) }}" class="bg-white p-4 rounded shadow flex items-end gap-3">
      <div>
        <label class="block text-sm text-slate-600">Monthly Statement (YYYY-MM)</label>
        <input type="month" name="month" class="border rounded px-3 py-2" value="{{ now()->format('Y-m') }}">
      </div>
      <button class="px-4 py-2 bg-slate-700 text-white rounded" type="submit">View Statement</button>
    </form>
  </div>
</div>
@endsection


