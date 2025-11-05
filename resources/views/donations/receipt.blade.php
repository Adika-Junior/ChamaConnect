@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
    <div class="flex items-center justify-between mb-6">
        <div class="text-xl font-semibold">Donation Receipt</div>
        <img src="/brand/chamaconnect-logo.svg" alt="Brand" class="h-8">
    </div>
    <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
            <div class="text-slate-500">Reference</div>
            <div class="font-medium">{{ $donation->reference }}</div>
        </div>
        <div>
            <div class="text-slate-500">Date</div>
            <div class="font-medium">{{ optional($donation->paid_at)->toDayDateTimeString() }}</div>
        </div>
        <div>
            <div class="text-slate-500">Amount</div>
            <div class="font-medium">{{ $donation->amountFormatted() }}</div>
        </div>
        <div>
            <div class="text-slate-500">Mpesa Receipt</div>
            <div class="font-medium">{{ $donation->mpesa_receipt }}</div>
        </div>
        <div class="col-span-2 border-t pt-4 mt-2">
            <div class="text-slate-500">Donor</div>
            <div class="font-medium">{{ $donation->donor_name ?? 'Anonymous' }}</div>
            @if($donation->donor_email)
            <div class="text-slate-600">{{ $donation->donor_email }}</div>
            @endif
            @if($donation->donor_phone)
            <div class="text-slate-600">{{ $donation->donor_phone }}</div>
            @endif
        </div>
    </div>
    <div class="mt-6">
        <a href="{{ route('donations.receipt.download', $donation->reference) }}" class="px-4 py-2 bg-blue-600 text-white rounded">Download</a>
    </div>
</div>
@endsection


