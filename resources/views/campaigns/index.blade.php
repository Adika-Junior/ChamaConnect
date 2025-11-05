@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 gap-3 flex-wrap">
        <h1 class="h3 mb-0">Campaigns</h1>
        <form method="GET" class="d-flex align-items-center gap-2 ms-auto">
            <input type="text" name="q" value="{{ $search ?? '' }}" class="form-control" placeholder="Search campaigns..." />
            <select name="sort" class="form-select">
                <option value="newest" {{ ($sort ?? '')==='newest' ? 'selected' : '' }}>Newest</option>
                <option value="goal" {{ ($sort ?? '')==='goal' ? 'selected' : '' }}>Goal amount</option>
                <option value="progress" {{ ($sort ?? '')==='progress' ? 'selected' : '' }}>Progress</option>
            </select>
            <button type="submit" class="btn btn-outline-primary">Apply</button>
        </form>
        @auth
            <a href="{{ route('campaigns.create') }}" class="btn btn-primary">Create Campaign</a>
        @endauth
    </div>

    <div class="row g-3">
        @forelse($campaigns as $campaign)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $campaign->title }}</h5>
                        <p class="card-text small">{{ Str::limit($campaign->description, 100) }}</p>
                        <div class="mb-2">
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $campaign->progress }}%"></div>
                            </div>
                            <small class="text-muted">KSh {{ number_format($campaign->current_amount, 0) }} of KSh {{ number_format($campaign->goal_amount, 0) }}</small>
                        </div>
                        <div class="mb-2">
                            <span class="badge bg-{{ $campaign->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($campaign->status) }}</span>
                        </div>
                        <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-sm btn-primary">View Campaign</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center text-muted py-5">
                    <p>No active campaigns at the moment.</p>
                </div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $campaigns->links() }}</div>
</div>
@endsection

