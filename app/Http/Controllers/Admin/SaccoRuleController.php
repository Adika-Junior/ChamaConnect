<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaccoRule;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SaccoRuleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', \App\Http\Middleware\RequireTwoFactor::class]);
    }

    public function index()
    {
        $rules = SaccoRule::orderBy('name')->get();
        return view('admin.sacco-rules.index', compact('rules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:100'],
            'slug' => ['nullable','string','max:100'],
        ]);

        $name = $validated['name'];
        $slug = $validated['slug'] ?: Str::slug($name, '_');
        if (SaccoRule::where('slug', $slug)->exists()) {
            return back()->withErrors(['slug' => 'Slug already exists'])->withInput();
        }

        SaccoRule::create(['name' => $name, 'slug' => $slug]);
        return back()->with('status', 'Rule added');
    }

    public function destroy(SaccoRule $rule)
    {
        $rule->delete();
        return back()->with('status', 'Rule deleted');
    }
}


