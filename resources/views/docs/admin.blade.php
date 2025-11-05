@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-2xl font-semibold mb-4">Admin Handbook</h1>
    <ul class="list-disc ml-6 space-y-2 text-slate-700">
        <li>Approve users and groups; manage roles and permissions.</li>
        <li>Review webhooks dashboard; retry failed callbacks.</li>
        <li>Run exports: donations and contributions; review summaries.</li>
        <li>Configure alerts and notifications (Slack/email).</li>
        <li>Backups: Daily DB dump at 02:00 to the configured disk. Manual run: <code>php artisan backup:db --disk=local</code>. Restore: download <code>db_*.sql.gz</code>, gunzip, then import using <code>mysql -hHOST -uUSER -p DB &lt; dump.sql</code>.</li>
    </ul>
</div>
@endsection


