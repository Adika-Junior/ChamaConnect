@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-3">{{ $group->name }} - Financial Report</h1>

    <div class="card mb-3">
        <div class="card-header">Summary</div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-4">
                    <div class="h4 text-success">KSh {{ number_format($group->total_contributions, 2) }}</div>
                    <small class="text-muted">Total Contributions</small>
                </div>
                <div class="col-4">
                    <div class="h4 text-danger">KSh {{ number_format($group->total_expenses, 2) }}</div>
                    <small class="text-muted">Total Expenses</small>
                </div>
                <div class="col-4">
                    <div class="h4 text-primary">KSh {{ number_format($group->balance, 2) }}</div>
                    <small class="text-muted">Balance</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Member Contributions</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($memberContributions as $member)
                                <tr>
                                    <td>{{ $member->name }}</td>
                                    <td class="text-end">KSh {{ number_format($member->pivot->total_contributed, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Expenses by Category</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expensesByCategory as $expense)
                                <tr>
                                    <td>{{ $expense->category ?: 'Uncategorized' }}</td>
                                    <td class="text-end">KSh {{ number_format($expense->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

