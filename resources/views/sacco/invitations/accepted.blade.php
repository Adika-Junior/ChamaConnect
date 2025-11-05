@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6">
  @if($error)
    <div class="bg-red-100 border border-red-200 text-red-800 p-3 rounded mb-4">{{ $error }}</div>
  @else
    <div class="bg-green-100 border border-green-200 text-green-800 p-3 rounded mb-4">Invitation accepted.</div>
    @if($group)
      <div>You have accepted an invitation to join <span class="font-semibold">{{ $group->name }}</span>. You can now sign in or create an account to access the group.</div>
    @endif
  @endif
</div>
@endsection


