<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecurringContributionRule;
use Illuminate\Http\Request;

class RecurringContributionRuleController extends Controller
{
    public function index()
    {
        $rules = RecurringContributionRule::query()->orderByDesc('id')->paginateDefault();
        return view('admin.recurring_rules.index', compact('rules'));
    }

    public function create()
    {
        return view('admin.recurring_rules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_name' => 'nullable|string|max:120',
            'recipient_email' => 'nullable|email',
            'recipient_phone' => 'nullable|string|max:50',
            'amount_cents' => 'required|integer|min:100',
            'currency' => 'required|string|size:3',
            'interval' => 'required|in:weekly,monthly,quarterly',
            'day_of_month' => 'nullable|integer|min:1|max:28',
            'weekday' => 'nullable|integer|min:0|max:6',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,paused',
        ]);

        $validated['next_run_at'] = now();
        RecurringContributionRule::create($validated);
        return redirect()->route('admin.recurring_rules.index')->with('status', 'Rule created');
    }

    public function edit(RecurringContributionRule $rule)
    {
        return view('admin.recurring_rules.edit', compact('rule'));
    }

    public function update(Request $request, RecurringContributionRule $rule)
    {
        $validated = $request->validate([
            'recipient_name' => 'nullable|string|max:120',
            'recipient_email' => 'nullable|email',
            'recipient_phone' => 'nullable|string|max:50',
            'amount_cents' => 'required|integer|min:100',
            'currency' => 'required|string|size:3',
            'interval' => 'required|in:weekly,monthly,quarterly',
            'day_of_month' => 'nullable|integer|min:1|max:28',
            'weekday' => 'nullable|integer|min:0|max:6',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,paused',
        ]);

        $rule->update($validated);
        return redirect()->route('admin.recurring_rules.index')->with('status', 'Rule updated');
    }

    public function destroy(RecurringContributionRule $rule)
    {
        $rule->delete();
        return redirect()->route('admin.recurring_rules.index')->with('status', 'Rule deleted');
    }
}


