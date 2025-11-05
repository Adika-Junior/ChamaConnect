@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Contributions</h1>
        <a href="{{ route('contributions.create') }}" class="btn btn-primary">New Contribution</a>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Target</th>
                <th>Collected</th>
                <th>Deadline</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        @foreach($contributions as $c)
            <tr>
                <td><a href="{{ route('contributions.show', $c) }}">{{ $c->title }}</a></td>
                <td>{{ $c->category }}</td>
                <td>{{ ucfirst(str_replace('_',' ', $c->status)) }}</td>
                <td>{{ number_format($c->target_amount, 2) }}</td>
                <td>{{ number_format($c->collected_amount, 2) }}</td>
                <td>{{ optional($c->deadline)->format('Y-m-d H:i') }}</td>
                <td class="text-end">
                    <a class="btn btn-sm btn-secondary" href="{{ route('contributions.edit', $c) }}">Edit</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{ $contributions->links() }}
</div>
@endsection


