@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">New Recurring Rule</h1>
    <form method="POST" action="{{ route('admin.recurring_rules.store') }}" class="space-y-4 bg-white p-4 rounded shadow">
        @csrf
        <div>
            <label class="block text-sm text-slate-600">Recipient Name</label>
            <input type="text" name="recipient_name" class="border rounded px-3 py-2 w-full">
        </div>
        <div>
            <label class="block text-sm text-slate-600">Recipient Email</label>
            <input type="email" name="recipient_email" class="border rounded px-3 py-2 w-full">
        </div>
        <div>
            <label class="block text-sm text-slate-600">Recipient Phone</label>
            <input type="text" name="recipient_phone" class="border rounded px-3 py-2 w-full">
        </div>
        <div>
            <label class="block text-sm text-slate-600">
                Amount (KES cents)
                <span class="badge bg-info ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Amount in Kenyan Shillings cents (e.g., 1000 = KES 10.00). This will be automatically deducted at the specified interval.">ℹ️</span>
            </label>
            <input type="number" name="amount_cents" class="border rounded px-3 py-2 w-full" required>
        </div>
        <div>
            <label class="block text-sm text-slate-600">
                Interval
                <span class="badge bg-info ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="How often the contribution should be processed. Weekly runs on the specified weekday; Monthly runs on the day of month; Quarterly every 3 months.">ℹ️</span>
            </label>
            <select name="interval" class="border rounded px-3 py-2 w-full" required>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
                <option value="quarterly">Quarterly</option>
            </select>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-slate-600">Day of Month (1-28)</label>
                <input type="number" name="day_of_month" min="1" max="28" class="border rounded px-3 py-2 w-full">
            </div>
            <div>
                <label class="block text-sm text-slate-600">Weekday (0=Sun..6=Sat)</label>
                <input type="number" name="weekday" min="0" max="6" class="border rounded px-3 py-2 w-full">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-slate-600">Start Date</label>
                <input type="date" name="start_date" class="border rounded px-3 py-2 w-full" required>
            </div>
            <div>
                <label class="block text-sm text-slate-600">End Date (optional)</label>
                <input type="date" name="end_date" class="border rounded px-3 py-2 w-full">
            </div>
        </div>
        <div>
            <label class="block text-sm text-slate-600">Status</label>
            <select name="status" class="border rounded px-3 py-2 w-full" required>
                <option value="active">Active</option>
                <option value="paused">Paused</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
    </form>
</div>
@endsection
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });
});
</script>
@endsection


