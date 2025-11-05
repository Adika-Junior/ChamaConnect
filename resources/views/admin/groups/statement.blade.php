@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h5 mb-0">Monthly Statement - {{ $group->name }} ({{ $start->format('M Y') }})</h1>
    <button class="btn btn-primary" onclick="window.print()">Print / Save PDF</button>
  </div>
  <div class="bg-white p-4 rounded shadow">
    <div class="mb-3 text-muted">Period: {{ $start->toDateString() }} → {{ $end->toDateString() }}</div>
    <div class="row g-3">
      <div class="col-md-6">
        <div class="border rounded p-3 h-100">
          <div class="fw-semibold mb-2">Payments</div>
          @if($payments->count())
          <table class="table table-sm">
            <thead><tr><th>Date</th><th>Amount</th><th>Method</th></tr></thead>
            <tbody>
              @foreach($payments as $p)
              <tr>
                <td>{{ optional($p->created_at)->toDateString() }}</td>
                <td>KES {{ number_format(($p->amount ?? ($p->amount_cents ? $p->amount_cents/100 : 0)), 2) }}</td>
                <td>{{ $p->method ?? 'mpesa' }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          @else
          <div class="text-muted">No payments in this period.</div>
          @endif
        </div>
      </div>
      <div class="col-md-6">
        <div class="border rounded p-3 h-100">
          <div class="fw-semibold mb-2">Expenses</div>
          @if($expenses->count())
          <table class="table table-sm">
            <thead><tr><th>Date</th><th>Category</th><th>Amount</th></tr></thead>
            <tbody>
              @foreach($expenses as $e)
              <tr>
                <td>{{ optional($e->created_at)->toDateString() }}</td>
                <td>{{ $e->category }}</td>
                <td>KES {{ number_format((float)$e->amount, 2) }}</td>
              </tr>
              @endforeach
            </tbody>
          </table>
          @else
          <div class="text-muted">No expenses in this period.</div>
          @endif
        </div>
      </div>
    </div>
    <div class="mt-3 d-flex justify-content-end gap-4">
      <div><span class="text-muted">Total Payments:</span> <span class="fw-semibold">KES {{ number_format($totalPayments, 2) }}</span></div>
      <div><span class="text-muted">Total Expenses:</span> <span class="fw-semibold">KES {{ number_format($totalExpenses, 2) }}</span></div>
      <div><span class="text-muted">Net:</span> <span class="fw-semibold">KES {{ number_format($totalPayments - $totalExpenses, 2) }}</span></div>
    </div>
  </div>
</div>
@endsection


