<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContributionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $query = Contribution::with(['organizer', 'approver', 'meeting'])
            ->orderByDesc('created_at');

        if (!$user->isAdmin()) {
            $query->where(function ($q) use ($user) {
                $q->where('organizer_id', $user->id)
                  ->orWhere('approver_id', $user->id)
                  ->orWhereHas('meeting.participants', function ($q2) use ($user) {
                      $q2->where('users.id', $user->id);
                  });
            });
        }

        $contributions = $query->paginate(15);
        return view('contributions.index', compact('contributions'));
    }

    public function create(Request $request)
    {
        $meetings = Meeting::orderByDesc('scheduled_at')->pluck('title', 'id');
        $groups = \App\Models\Group::whereHas('members', function($query) {
            $query->where('user_id', Auth::id());
        })->orWhere('created_by', Auth::id())
        ->pluck('name', 'id');
        
        $groupId = $request->query('group_id');
        $saccoRules = \App\Models\SaccoRule::allSlugs();
        if (empty($saccoRules)) {
            $saccoRules = config('sacco.rules', []);
        }
        
        return view('contributions.create', compact('meetings', 'groups', 'groupId', 'saccoRules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'kind' => ['required', 'in:one_time,sacco'],
            'sacco_rule' => ['nullable', 'string', 'max:100'],
            'sacco_rule_select' => ['nullable', 'string', 'max:100'],
            'sacco_rule_custom' => ['nullable', 'string', 'max:100'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'deadline' => ['required', 'date', 'after:now'],
            'meeting_id' => ['nullable', 'exists:meetings,id'],
            'group_id' => ['nullable', 'exists:groups,id'],
        ]);

        // Enforce SACCO constraints
        if (($validated['kind'] ?? 'one_time') === 'sacco') {
            if (empty($validated['group_id'])) {
                return back()->withErrors(['group_id' => 'Group is required for SACCO contributions.'])->withInput();
            }
            $group = \App\Models\Group::find($validated['group_id']);
            if (!$group || $group->type !== 'sacco') {
                return back()->withErrors(['group_id' => 'Selected group must be a SACCO.'])->withInput();
            }
            // Determine sacco_rule from select/custom
            $predefined = config('sacco.rules', []);
            $selected = $request->input('sacco_rule_select');
            $custom = $request->input('sacco_rule_custom');
            if ($selected === 'custom') {
                if (empty($custom)) {
                    return back()->withErrors(['sacco_rule_custom' => 'Please specify the custom SACCO rule.'])->withInput();
                }
                $validated['sacco_rule'] = $custom;
            } else {
                if (!in_array($selected, $predefined, true)) {
                    return back()->withErrors(['sacco_rule_select' => 'Invalid SACCO rule selected.'])->withInput();
                }
                $validated['sacco_rule'] = $selected;
            }
        } else {
            // For one-time, clear sacco_rule
            $validated['sacco_rule'] = null;
        }

        $contribution = new Contribution($validated);
        $contribution->organizer_id = Auth::id();
        $contribution->status = 'pending_approval';
        $contribution->collected_amount = $contribution->collected_amount ?? 0;
        $contribution->save();

        return redirect()->route('contributions.show', $contribution)->with('status', 'Contribution created and pending approval.');
    }

    public function show(Contribution $contribution)
    {
        $this->authorize('view', $contribution);
        $contribution->load(['organizer', 'approver', 'meeting.participants', 'participants', 'pledges.user', 'group']);
        return view('contributions.show', compact('contribution'));
    }

    public function edit(Contribution $contribution)
    {
        $this->authorize('update', $contribution);
        $meetings = Meeting::orderByDesc('start_time')->pluck('title', 'id');
        $saccoRules = \App\Models\SaccoRule::allSlugs();
        if (empty($saccoRules)) {
            $saccoRules = config('sacco.rules', []);
        }
        return view('contributions.edit', compact('contribution', 'meetings', 'saccoRules'));
    }

    public function update(Request $request, Contribution $contribution)
    {
        $this->authorize('update', $contribution);
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'kind' => ['required', 'in:one_time,sacco'],
            'sacco_rule' => ['nullable', 'string', 'max:100'],
            'sacco_rule_select' => ['nullable', 'string', 'max:100'],
            'sacco_rule_custom' => ['nullable', 'string', 'max:100'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'deadline' => ['required', 'date', 'after:now'],
            'meeting_id' => ['nullable', 'exists:meetings,id'],
            'status' => ['sometimes', 'in:pending_approval,approved,rejected,active,closed'],
        ]);

        if (($validated['kind'] ?? $contribution->kind ?? 'one_time') === 'sacco') {
            $groupId = $request->input('group_id', $contribution->group_id);
            if (empty($groupId)) {
                return back()->withErrors(['group_id' => 'Group is required for SACCO contributions.'])->withInput();
            }
            $group = \App\Models\Group::find($groupId);
            if (!$group || $group->type !== 'sacco') {
                return back()->withErrors(['group_id' => 'Selected group must be a SACCO.'])->withInput();
            }
            $predefined = config('sacco.rules', []);
            $selected = $request->input('sacco_rule_select', $contribution->sacco_rule);
            $custom = $request->input('sacco_rule_custom');
            if ($selected === 'custom') {
                if (empty($custom)) {
                    return back()->withErrors(['sacco_rule_custom' => 'Please specify the custom SACCO rule.'])->withInput();
                }
                $validated['sacco_rule'] = $custom;
            } else {
                if (!in_array($selected, $predefined, true)) {
                    return back()->withErrors(['sacco_rule_select' => 'Invalid SACCO rule selected.'])->withInput();
                }
                $validated['sacco_rule'] = $selected;
            }
        } else {
            $validated['sacco_rule'] = null;
        }

        $contribution->fill($validated);
        $contribution->save();

        return redirect()->route('contributions.show', $contribution)->with('status', 'Contribution updated.');
    }

    public function destroy(Contribution $contribution)
    {
        $this->authorize('delete', $contribution);
        $contribution->delete();
        return redirect()->route('contributions.index')->with('status', 'Contribution deleted.');
    }

    public function addParticipant(Request $request, Contribution $contribution)
    {
        $this->authorize('update', $contribution);
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
        ]);
        // Optional: Restrict to meeting participants if linked to a meeting
        if ($contribution->meeting && !$contribution->meeting->participants()->where('users.id', $validated['user_id'])->exists()) {
            return back()->withErrors(['user_id' => 'User is not a participant in the meeting.']);
        }
        $contribution->participants()->syncWithoutDetaching([$validated['user_id'] => [
            'amount_contributed' => 0,
        ]]);
        return back()->with('status', 'Participant added.');
    }

    public function removeParticipant(Contribution $contribution, \App\Models\User $user)
    {
        $this->authorize('update', $contribution);
        $contribution->participants()->detach($user->id);
        return back()->with('status', 'Participant removed.');
    }

    public function report(Contribution $contribution)
    {
        $this->authorize('view', $contribution);
        $contribution->load(['payments.user', 'organizer', 'meeting']);

        $total = (float) $contribution->collected_amount;
        $target = (float) $contribution->target_amount;
        $percent = $target > 0 ? round(($total / $target) * 100, 2) : 0;

        $byMethod = $contribution->payments
            ->groupBy('payment_method')
            ->map(fn ($g) => [
                'count' => $g->count(),
                'amount' => (float) $g->sum('amount'),
            ]);

        return view('contributions.report', compact('contribution', 'total', 'target', 'percent', 'byMethod'));
    }

    public function exportCsv(Contribution $contribution)
    {
        $this->authorize('view', $contribution);
        $contribution->load(['payments.user']);

        $filename = 'contribution_'.$contribution->id.'_payments.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($contribution) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Paid At', 'Contributor', 'Amount', 'Method', 'Reference']);
            foreach ($contribution->payments()->orderByDesc('paid_at')->get() as $p) {
                fputcsv($out, [
                    optional($p->paid_at)->toDateTimeString(),
                    optional($p->user)->name,
                    number_format($p->amount, 2, '.', ''),
                    strtoupper($p->payment_method),
                    $p->reference,
                ]);
            }
            fclose($out);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }
}


