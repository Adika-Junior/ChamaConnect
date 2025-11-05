@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-3">Campaigns</h1>

  <form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
      <input type="search" name="q" value="{{ $q }}" class="form-control" placeholder="Search campaigns">
    </div>
    <div class="col-md-3">
      <select name="status" class="form-select">
        <option value="">Any status</option>
        <option value="active" @if($status==='active') selected @endif>Active</option>
        <option value="closed" @if($status==='closed') selected @endif>Closed</option>
        <option value="draft" @if($status==='draft') selected @endif>Draft</option>
      </select>
    </div>
    <div class="col-md-3">
      <input type="text" name="tag" value="{{ $tag }}" class="form-control" placeholder="Filter by tag e.g. education">
    </div>
    <div class="col-md-2 d-grid">
      <button class="btn btn-primary">Apply</button>
    </div>
  </form>
  <form method="POST" action="{{ route('campaigns.pledges.store', ['campaign' => request('campaign') ?? ($campaigns->first()?->id ?? '')]) }}" class="d-none">
    @csrf
    <input type="hidden" name="_hp" value="">
    <input type="hidden" name="_ts" value="{{ time() }}">
  </form>

  @if($tags->isNotEmpty())
  <div class="mb-3">
    <div class="small text-muted mb-1">Popular tags</div>
    @foreach($tags as $t)
      <a href="{{ route('campaigns.hub', array_filter(['q'=>$q,'status'=>$status,'tag'=>$t])) }}" class="badge text-bg-light me-1 mb-1">#{{ $t }}</a>
    @endforeach
  </div>
  @endif

  @if($campaigns->count() === 0)
    <div class="alert alert-info">
      No campaigns found. Try clearing filters or searching different keywords. If you're an organizer, <a href="#">create a campaign</a>.
    </div>
  @else
    <div class="row row-cols-1 row-cols-md-3 g-3">
      @foreach($campaigns as $c)
      <div class="col">
        <div class="card h-100">
          <div class="card-body">
            <h5 class="card-title">{{ $c->title }}</h5>
            <p class="card-text text-muted">{{ Str::limit($c->description, 120) }}</p>
            @if(is_array($c->tags))
            <div class="mt-2">
              @foreach($c->tags as $ct)
                <span class="badge text-bg-secondary me-1">#{{ $ct }}</span>
              @endforeach
            </div>
            @endif
          </div>
          <div class="card-footer bg-white">
            <a href="{{ route('campaigns.show', $c->id) }}" class="btn btn-outline-primary btn-sm">View</a>
          </div>
        </div>
      </div>
      @endforeach
    </div>

    <div class="mt-3">{{ $campaigns->withQueryString()->links() }}</div>
  @endif
</div>
@endsection


