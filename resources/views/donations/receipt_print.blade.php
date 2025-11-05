<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Donation Receipt {{ $donation->reference }}</title>
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica Neue, Arial; color: #0f172a; }
        .container { max-width: 720px; margin: 24px auto; padding: 24px; border: 1px solid #e2e8f0; border-radius: 8px; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; }
        .muted { color: #64748b; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .mt { margin-top: 16px; }
        .btn { display: inline-block; padding: 8px 12px; background: #2563eb; color: white; border-radius: 6px; text-decoration: none; }
    </style>
    <script>window.onload = function(){ window.print(); };</script>
    </head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <div style="font-weight:600;font-size:18px;">Donation Receipt</div>
                <div class="muted">Reference: {{ $donation->reference }}</div>
            </div>
            <img src="/brand/chamaconnect-logo.svg" alt="Brand" style="height:32px;">
        </div>
        <div class="row">
            <div>
                <div class="muted">Date</div>
                <div>{{ optional($donation->paid_at)->toDayDateTimeString() }}</div>
            </div>
            <div>
                <div class="muted">Amount</div>
                <div>{{ $donation->amountFormatted() }}</div>
            </div>
            <div>
                <div class="muted">Mpesa Receipt</div>
                <div>{{ $donation->mpesa_receipt }}</div>
            </div>
            <div>
                <div class="muted">Campaign ID</div>
                <div>{{ $donation->campaign_id }}</div>
            </div>
        </div>
        <div class="mt">
            <div class="muted">Donor</div>
            <div>{{ $donation->donor_name ?? 'Anonymous' }}</div>
            @if($donation->donor_email)
            <div class="muted">{{ $donation->donor_email }}</div>
            @endif
            @if($donation->donor_phone)
            <div class="muted">{{ $donation->donor_phone }}</div>
            @endif
        </div>
    </div>
</body>
</html>


