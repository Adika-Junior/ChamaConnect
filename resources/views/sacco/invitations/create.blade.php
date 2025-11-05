@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6">
  <h1 class="text-2xl font-semibold mb-4">Invite Member to {{ $group->name }}</h1>
  <form method="POST" action="{{ route('sacco.invitations.store', $group) }}" class="space-y-4 bg-white p-4 rounded shadow">
    @csrf
    <div>
      <label class="block text-sm text-slate-600">Email</label>
      <input type="email" name="email" class="border rounded px-3 py-2 w-full" placeholder="name@example.com">
    </div>
    <div>
      <label class="block text-sm text-slate-600">Phone</label>
      <input type="text" name="phone" class="border rounded px-3 py-2 w-full" placeholder="e.g., +2547...">
      <div class="text-xs text-slate-500">Provide either email or phone.</div>
    </div>
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Send Invitation</button>
  </form>
</div>
@endsection


