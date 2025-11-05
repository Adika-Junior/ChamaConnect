@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <div class="h5 mb-0">{{ $campaign->title }}</div>
      <div class="text-muted">Donor Receipt</div>
    </div>
    <img src="/brand/chamaconnect-logo.svg" alt="Brand" style="height: 32px;">
  </div>
  <div class="row g-3">
    <div class="col-md-6">
      <div class="text-muted">Reference</div>
      <div class="fw-semibold">{{ $donation->reference }}</div>
    </div>
    <div class="col-md-6">
      <div class="text-muted">Date</div>
      <div class="fw-semibold">{{ optional($donation->paid_at)->toDayDateTimeString() }}</div>
    </div>
    <div class="col-md-6">
      <div class="text-muted">Amount</div>
      <div class="fw-semibold">{{ $donation->amountFormatted() }}</div>
    </div>
    <div class="col-md-6">
      <div class="text-muted">Mpesa Receipt</div>
      <div class="fw-semibold">{{ $donation->mpesa_receipt }}</div>
    </div>
  </div>
  <hr class="my-4">
  <div>
    <div class="text-muted">Donor</div>
    <div class="fw-semibold">{{ $donation->donor_name ?? 'Anonymous' }}</div>
    @if($donation->donor_email)
    <div class="text-muted">{{ $donation->donor_email }}</div>
    @endif
    @if($donation->donor_phone)
    <div class="text-muted">{{ $donation->donor_phone }}</div>
    @endif
  </div>
</div>
@endsection


