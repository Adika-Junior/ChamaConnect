@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <h1 class="text-3xl font-semibold mb-4">Documentation</h1>
    <p class="text-slate-600 mb-6">Quick start, feature guides, and admin handbook.</p>

    <ul class="list-disc ml-6 space-y-2">
        <li><a class="text-blue-600" href="{{ route('docs.quickstart') }}">Quick Start</a></li>
        <li><a class="text-blue-600" href="{{ route('docs.features') }}">Feature Guides</a></li>
        <li><a class="text-blue-600" href="{{ route('docs.admin') }}">Admin Handbook</a></li>
        <li><a class="text-blue-600" href="{{ route('docs.walkthroughs') }}">Video Walkthroughs</a></li>
    </ul>
</div>
@endsection


