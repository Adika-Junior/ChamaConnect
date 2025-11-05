@extends('layouts.app')

@section('content')
<div class="container">
    <h1>New Contribution</h1>

    <form method="POST" action="{{ route('contributions.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
            @error('title')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <input type="text" name="category" class="form-control" value="{{ old('category') }}" required>
            @error('category')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Kind</label>
                <select name="kind" id="kind" class="form-select" required>
                    <option value="one_time" @selected(old('kind','one_time')==='one_time')>One-time (Event/Project)</option>
                    <option value="sacco" @selected(old('kind')==='sacco')>SACCO (Rule-based)</option>
                </select>
                @error('kind')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6" id="saccoRuleWrap" style="display:none;">
                <label class="form-label">SACCO Rule</label>
                @php($rules = isset($saccoRules) ? $saccoRules : config('sacco.rules', []))
                <select name="sacco_rule_select" id="sacco_rule_select" class="form-select">
                    @foreach($rules as $rule)
                        <option value="{{ $rule }}" @selected(old('sacco_rule_select')===$rule)>{{ str_replace('_',' ', ucfirst($rule)) }}</option>
                    @endforeach
                    <option value="custom" @selected(old('sacco_rule_select')==='custom')>Other (custom)</option>
                </select>
                @error('sacco_rule_select')<div class="text-danger">{{ $message }}</div>@enderror
                <input type="text" name="sacco_rule_custom" id="sacco_rule_custom" class="form-control mt-2" value="{{ old('sacco_rule_custom') }}" placeholder="Specify custom rule" style="display:none;">
                @error('sacco_rule_custom')<div class="text-danger">{{ $message }}</div>@enderror
                <small class="text-muted">Select a predefined rule or specify a custom one</small>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Target Amount</label>
            <input type="number" step="0.01" name="target_amount" class="form-control" value="{{ old('target_amount') }}" required>
            @error('target_amount')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Deadline</label>
            <input type="datetime-local" name="deadline" class="form-control" value="{{ old('deadline') }}" required>
            @error('deadline')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Group/SACCO (optional)</label>
                <select name="group_id" class="form-select">
                    <option value="">-- None --</option>
                    @if(isset($groupId) && $groupId)
                        @php($selectedGroup = \App\Models\Group::find($groupId))
                        @if($selectedGroup)
                            <option value="{{ $selectedGroup->id }}" selected>{{ $selectedGroup->name }}</option>
                        @endif
                    @endif
                    @foreach($groups ?? [] as $id => $name)
                        <option value="{{ $id }}" @selected(old('group_id')==$id || (isset($groupId) && $groupId == $id))>{{ $name }}</option>
                    @endforeach
                </select>
                @error('group_id')<div class="text-danger">{{ $message }}</div>@enderror
                <small class="text-muted">Link this contribution to a specific group/SACCO</small>
            </div>
            <div class="col-md-6">
                <label class="form-label">Meeting (optional)</label>
                <select name="meeting_id" class="form-select">
                    <option value="">-- None --</option>
                    @foreach($meetings ?? [] as $id => $title)
                        <option value="{{ $id }}" @selected(old('meeting_id')==$id)>{{ $title }}</option>
                    @endforeach
                </select>
                @error('meeting_id')<div class="text-danger">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
            @error('description')<div class="text-danger">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn btn-primary">Create</button>
        <a href="{{ route('contributions.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const kind = document.getElementById('kind');
  const saccoWrap = document.getElementById('saccoRuleWrap');
  const ruleSelect = document.getElementById('sacco_rule_select');
  const ruleCustom = document.getElementById('sacco_rule_custom');
  function updateSaccoVisibility() {
    saccoWrap.style.display = kind.value === 'sacco' ? 'block' : 'none';
    if (ruleSelect) {
      ruleCustom.style.display = ruleSelect.value === 'custom' ? 'block' : 'none';
    }
  }
  kind.addEventListener('change', updateSaccoVisibility);
  if (ruleSelect) {
    ruleSelect.addEventListener('change', updateSaccoVisibility);
  }
  updateSaccoVisibility();
});
</script>
@endsection


