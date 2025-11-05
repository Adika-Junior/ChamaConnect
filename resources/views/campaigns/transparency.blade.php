@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ $campaign->title }} - Transparency Dashboard</h1>
        <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-secondary">Back to Campaign</a>
    </div>

    <div class="card mb-3">
        <div class="card-header">Financial Summary</div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-4">
                    <div class="h4 text-success">KSh {{ number_format($donationsTotal, 2) }}</div>
                    <small class="text-muted">Total Donations</small>
                </div>
                <div class="col-4">
                    <div class="h4 text-danger">KSh {{ number_format($expensesTotal, 2) }}</div>
                    <small class="text-muted">Total Expenses</small>
                </div>
                <div class="col-4">
                    <div class="h4 text-primary">KSh {{ number_format($netAmount, 2) }}</div>
                    <small class="text-muted">Net Amount</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Top Donors</div>
                <div class="card-body">
                    <ol class="list-group list-group-numbered">
                        @foreach($topDonors as $donor)
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">{{ $donor->donor_name }}</div>
                                    <small>{{ $donor->count }} donation(s)</small>
                                </div>
                                <span class="badge bg-primary rounded-pill">KSh {{ number_format($donor->total, 0) }}</span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Expenses</div>
                <div class="card-body">
                    @if($campaign->expenses->count() > 0)
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($campaign->expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->title }}</td>
                                        <td class="text-end">KSh {{ number_format($expense->amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">No expenses recorded.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

