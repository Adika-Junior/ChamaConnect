@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Contributions Summary</h1>
    <form method="GET" action="{{ route('admin.contributions.summary') }}" class="space-y-4 bg-white p-4 rounded shadow mb-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-slate-600">From</label>
                <input type="date" name="from" value="{{ request('from') }}" class="border rounded px-3 py-2 w-full" required>
            </div>
            <div>
                <label class="block text-sm text-slate-600">To</label>
                <input type="date" name="to" value="{{ request('to') }}" class="border rounded px-3 py-2 w-full" required>
            </div>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Run</button>
    </form>

    @isset($totals)
    <div class="bg-white p-4 rounded shadow">
        <div class="text-sm text-slate-600 mb-2">Range: {{ $from }} → {{ $to }}</div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <div class="text-slate-500">Pending</div>
                <div class="text-lg font-semibold">KES {{ $totals['pending'] }}</div>
            </div>
            <div>
                <div class="text-slate-500">Paid</div>
                <div class="text-lg font-semibold">KES {{ $totals['paid'] }}</div>
            </div>
            <div>
                <div class="text-slate-500">Overdue</div>
                <div class="text-lg font-semibold">KES {{ $totals['overdue'] }}</div>
            </div>
            <div>
                <div class="text-slate-500">Cancelled</div>
                <div class="text-lg font-semibold">KES {{ $totals['cancelled'] }}</div>
            </div>
        </div>
        <div class="mt-4 text-slate-600">Total pledges: {{ $totals['count'] }}</div>
    </div>
    @endisset
</div>
@endsection


