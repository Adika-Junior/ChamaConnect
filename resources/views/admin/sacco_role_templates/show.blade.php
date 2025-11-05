@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
  <h1 class="text-2xl font-semibold mb-4">Edit Role Template: {{ $template->display_name }}</h1>
  <form method="POST" action="{{ route('admin.sacco_role_templates.update', $template) }}" class="space-y-4 bg-white p-4 rounded shadow">
    @csrf
    @method('PUT')
    <div>
      <label class="block text-sm text-slate-600 mb-2">Permissions (one per line)</label>
      <textarea name="permissions_text" rows="10" class="border rounded px-3 py-2 w-full font-mono text-sm">{{ implode("\n", $template->permissions ?? []) }}</textarea>
      <div class="text-xs text-slate-500 mt-1">Examples: groups.view, groups.manage, contributions.create, payments.view</div>
    </div>
    <div>
      <label class="block text-sm text-slate-600">Description</label>
      <textarea name="description" class="border rounded px-3 py-2 w-full">{{ $template->description }}</textarea>
    </div>
    <button class="px-4 py-2 bg-blue-600 text-white rounded">Save</button>
  </form>
</div>
@endsection

