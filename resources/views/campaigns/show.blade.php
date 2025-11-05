@extends('layouts.app')

@section('head')
@if(isset($meta))
<meta property="og:title" content="{{ $meta['title'] }}">
<meta property="og:description" content="{{ $meta['description'] }}">
<meta property="og:image" content="{{ $meta['image'] }}">
<meta property="og:url" content="{{ $meta['url'] }}">
<meta property="og:type" content="website">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $meta['title'] }}">
<meta name="twitter:description" content="{{ $meta['description'] }}">
<meta name="twitter:image" content="{{ $meta['image'] }}">
<meta name="description" content="{{ $meta['description'] }}">
@endif
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3 mb-0">{{ $campaign->title }}</h1>
        @if(auth()->check() && (auth()->user()->id === $campaign->organizer_id || auth()->user()->isAdmin()))
            <div>
                <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-secondary">Edit Campaign</a>
            </div>
        @endif
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <p>{{ $campaign->description }}</p>
                    <div class="mb-3">
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $campaign->progress }}%">
                                {{ number_format($campaign->progress, 1) }}%
                            </div>
                        </div>
                    </div>
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="h5">KSh {{ number_format($campaign->current_amount, 2) }}</div>
                            <small class="text-muted">Raised</small>
                        </div>
                        <div class="col-4">
                            <div class="h5">KSh {{ number_format($campaign->goal_amount, 2) }}</div>
                            <small class="text-muted">Goal</small>
                        </div>
                        <div class="col-4">
                            <div class="h5">KSh {{ number_format($campaign->remaining, 2) }}</div>
                            <small class="text-muted">Remaining</small>
                        </div>
                    </div>

                    @if($campaign->status === 'active')
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6>Make a Donation</h6>
                                <form method="POST" action="{{ route('campaigns.donate', $campaign) }}">
                                    @csrf
                                    <div class="mb-2">
                                        <input type="number" step="0.01" name="amount" class="form-control" placeholder="Amount (KSh)" required>
                                    </div>
                                    <div class="mb-2">
                                        <input type="text" name="phone" class="form-control" placeholder="Phone Number (254XXXXXXXXX)" required>
                                        <small class="text-muted">For M-Pesa payment</small>
                                    </div>
                                    <div class="mb-2">
                                        <textarea name="message" class="form-control" rows="2" placeholder="Optional message"></textarea>
                                    </div>
                                    @if($campaign->allow_anonymous)
                                        <div class="mb-2 form-check">
                                            <input type="checkbox" name="is_anonymous" id="is_anonymous" class="form-check-input">
                                            <label for="is_anonymous" class="form-check-label">Donate anonymously</label>
                                        </div>
                                    @endif
                                    <button type="submit" class="btn btn-primary">Donate via M-Pesa</button>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if(auth()->check() && (auth()->user()->id === $campaign->organizer_id || auth()->user()->isAdmin()))
                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Campaign Updates</span>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUpdateModal">Add Update</button>
                        </div>
                        <div class="card-body">
                            @if($campaign->updates->count() > 0)
                                @foreach($campaign->updates as $update)
                                    <div class="mb-3 pb-3 border-bottom">
                                        <h6>{{ $update->title }}</h6>
                                        <p class="mb-1">{{ $update->content }}</p>
                                        <small class="text-muted">By {{ $update->author->name }} • {{ $update->created_at->diffForHumans() }}</small>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">No updates yet.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Add Update Modal -->
                    <div class="modal fade" id="addUpdateModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" action="{{ route('campaigns.updates.store', $campaign) }}">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Add Campaign Update</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="update_title" class="form-label">Title</label>
                                            <input type="text" name="title" id="update_title" class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="update_content" class="form-label">Content</label>
                                            <textarea name="content" id="update_content" class="form-control" rows="4" required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Add Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">Recent Donations</div>
                <div class="card-body">
                    @if($campaign->donations->count() > 0)
                        <ul class="list-group">
                            @foreach($campaign->donations as $donation)
                                <li class="list-group-item d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $donation->display_name }}</strong>
                                        @if($donation->message)
                                            <div class="small text-muted">{{ $donation->message }}</div>
                                        @endif
                                        @if($donation->payment_status)
                                            <span class="badge {{ $donation->payment_status === 'completed' ? 'bg-success' : ($donation->payment_status === 'failed' ? 'bg-danger' : 'bg-warning text-dark') }}">{{ ucfirst($donation->payment_status) }}</span>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold">KSh {{ number_format($donation->amount, 2) }}</div>
                                        <small class="text-muted">{{ $donation->created_at->diffForHumans() }}</small>
                                        @if($donation->payment_status !== 'completed')
                                            <form method="POST" action="{{ route('campaigns.donations.resend', [$campaign, $donation]) }}" class="mt-2 d-flex gap-2">
                                                @csrf
                                                <input type="text" name="phone" class="form-control form-control-sm" placeholder="254XXXXXXXXX" required>
                                                <button type="submit" class="btn btn-sm btn-outline-primary">Resend</button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No donations yet.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <span>Donor Wall</span>
                  @if(auth()->check() && auth()->user()->isAdmin())
                  <a href="{{ route('admin.donor_wall.index', $campaign) }}" class="btn btn-sm btn-outline-primary">Moderate</a>
                  @endif
                </div>
                <div class="card-body">
                    @if($donations->count() > 0)
                        <ol class="list-group list-group-numbered">
                            @foreach($donations as $donation)
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="ms-2 me-auto">
                                        <div class="fw-bold">{{ $donation->donor_name }}</div>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">KSh {{ number_format($donation->total, 0) }}</span>
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <p class="text-muted">No donors yet.</p>
                    @endif
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-header">Share</div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-secondary" type="button" onclick="navigator.clipboard.writeText(window.location.href)">Copy Link</button>
                        <a class="btn btn-outline-primary" target="_blank" href="https://twitter.com/intent/tweet?text={{ urlencode($campaign->title) }}&url={{ urlencode(request()->fullUrl()) }}">Share on X</a>
                        <a class="btn btn-outline-success" target="_blank" href="https://wa.me/?text={{ urlencode($campaign->title.' '.request()->fullUrl()) }}">Share on WhatsApp</a>
                        <a class="btn btn-outline-primary" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}">Share on Facebook</a>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <a href="{{ route('campaigns.transparency', $campaign) }}" class="btn btn-outline-info w-100">Transparency Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

