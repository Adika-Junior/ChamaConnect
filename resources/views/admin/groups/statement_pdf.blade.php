<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monthly Statement - {{ $group->name }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .summary { margin-top: 20px; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Monthly Statement - {{ $group->name }}</h1>
    <p>Period: {{ $start->toDateString() }} to {{ $end->toDateString() }}</p>

    <h2>Payments</h2>
    @if($payments->count())
    <table>
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
    <p>No payments in this period.</p>
    @endif

    <h2>Expenses</h2>
    @if($expenses->count())
    <table>
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
    <p>No expenses in this period.</p>
    @endif

    <div class="summary">
        <p>Total Payments: KES {{ number_format($totalPayments, 2) }}</p>
        <p>Total Expenses: KES {{ number_format($totalExpenses, 2) }}</p>
        <p>Net: KES {{ number_format($totalPayments - $totalExpenses, 2) }}</p>
    </div>
</body>
</html>

