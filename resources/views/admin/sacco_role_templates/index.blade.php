@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6">
  <h1 class="text-2xl font-semibold mb-4">SACCO Role Templates</h1>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @foreach($templates as $t)
    <div class="bg-white p-4 rounded shadow">
      <div class="flex items-center justify-between mb-2">
        <h2 class="text-lg font-semibold">{{ $t->display_name }}</h2>
        <a class="text-blue-600 text-sm" href="{{ route('admin.sacco_role_templates.show', $t) }}">Edit</a>
      </div>
      <div class="text-sm text-slate-600 mb-2">{{ $t->description }}</div>
      <div class="text-xs text-slate-500">
        <strong>Permissions:</strong> {{ count($t->permissions ?? []) }} defined
      </div>
    </div>
    @endforeach
  </div>
</div>
@endsection

