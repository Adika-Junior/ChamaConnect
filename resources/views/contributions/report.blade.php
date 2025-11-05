@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Contribution Report: {{ $contribution->title }}</h1>
        <div>
            <button onclick="window.print()" class="btn btn-outline-secondary me-2">Print / Save PDF</button>
            <a href="{{ route('contributions.export.csv', $contribution) }}" class="btn btn-outline-primary">Download CSV</a>
            <a href="{{ route('contributions.show', $contribution) }}" class="btn btn-link">Back</a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Target</div>
                    <div class="h4">{{ number_format($target, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Collected</div>
                    <div class="h4">{{ number_format($total, 2) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Progress</div>
                    <div class="h4">{{ $percent }}%</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted">Deadline</div>
                    <div class="h5">{{ optional($contribution->deadline)->format('Y-m-d H:i') ?: '—' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Totals by Method</div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Method</th>
                        <th class="text-end">Count</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($byMethod as $method => $row)
                        <tr>
                            <td>{{ strtoupper($method) }}</td>
                            <td class="text-end">{{ $row['count'] }}</td>
                            <td class="text-end">{{ number_format($row['amount'], 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted">No payments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">Chart</div>
        <div class="card-body">
            <canvas id="byMethodChart" height="120"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="card-header">All Payments</div>
        <div class="card-body p-0">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Paid At</th>
                        <th>Contributor</th>
                        <th class="text-end">Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contribution->payments()->with('user')->orderByDesc('paid_at')->get() as $p)
                        <tr>
                            <td>{{ optional($p->paid_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ optional($p->user)->name }}</td>
                            <td class="text-end">{{ number_format($p->amount, 2) }}</td>
                            <td>{{ strtoupper($p->payment_method) }}</td>
                            <td>{{ $p->reference }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('byMethodChart');
    if (!ctx) return;
    const data = @json($byMethod);
    const labels = Object.keys(data).map(k => k.toUpperCase());
    const amounts = Object.values(data).map(v => Number(v.amount));
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: 'Amount',
                data: amounts,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });
});
</script>
<style>
@media print {
    .btn, nav, header, footer { display: none !important; }
    .card { box-shadow: none !important; border: 1px solid #ccc; }
}
</style>
@endpush


