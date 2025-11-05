@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Edit Recurring Rule #{{ $rule->id }}</h1>
    <form method="POST" action="{{ route('admin.recurring_rules.update', $rule) }}" class="space-y-4 bg-white p-4 rounded shadow">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-sm text-slate-600">Recipient Name</label>
            <input type="text" name="recipient_name" value="{{ old('recipient_name', $rule->recipient_name) }}" class="border rounded px-3 py-2 w-full">
        </div>
        <div>
            <label class="block text-sm text-slate-600">Recipient Email</label>
            <input type="email" name="recipient_email" value="{{ old('recipient_email', $rule->recipient_email) }}" class="border rounded px-3 py-2 w-full">
        </div>
        <div>
            <label class="block text-sm text-slate-600">Recipient Phone</label>
            <input type="text" name="recipient_phone" value="{{ old('recipient_phone', $rule->recipient_phone) }}" class="border rounded px-3 py-2 w-full">
        </div>
        <div>
            <label class="block text-sm text-slate-600">Amount (KES cents)</label>
            <input type="number" name="amount_cents" value="{{ old('amount_cents', $rule->amount_cents) }}" class="border rounded px-3 py-2 w-full" required>
        </div>
        <div>
            <label class="block text-sm text-slate-600">Interval</label>
            <select name="interval" class="border rounded px-3 py-2 w-full" required>
                <option value="weekly" @if($rule->interval==='weekly') selected @endif>Weekly</option>
                <option value="monthly" @if($rule->interval==='monthly') selected @endif>Monthly</option>
                <option value="quarterly" @if($rule->interval==='quarterly') selected @endif>Quarterly</option>
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-slate-600">Day of Month (1-28)</label>
                <input type="number" name="day_of_month" min="1" max="28" value="{{ old('day_of_month', $rule->day_of_month) }}" class="border rounded px-3 py-2 w-full">
            </div>
            <div>
                <label class="block text-sm text-slate-600">Weekday (0=Sun..6=Sat)</label>
                <input type="number" name="weekday" min="0" max="6" value="{{ old('weekday', $rule->weekday) }}" class="border rounded px-3 py-2 w-full">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-slate-600">Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date', optional($rule->start_date)->toDateString()) }}" class="border rounded px-3 py-2 w-full" required>
            </div>
            <div>
                <label class="block text-sm text-slate-600">End Date (optional)</label>
                <input type="date" name="end_date" value="{{ old('end_date', optional($rule->end_date)->toDateString()) }}" class="border rounded px-3 py-2 w-full">
            </div>
        </div>
        <div>
            <label class="block text-sm text-slate-600">Status</label>
            <select name="status" class="border rounded px-3 py-2 w-full" required>
                <option value="active" @if($rule->status==='active') selected @endif>Active</option>
                <option value="paused" @if($rule->status==='paused') selected @endif>Paused</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
    </form>
</div>
@endsection


