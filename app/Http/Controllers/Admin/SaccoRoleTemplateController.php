<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GroupRoleTemplate;
use Illuminate\Http\Request;

class SaccoRoleTemplateController extends Controller
{
    public function index()
    {
        $templates = GroupRoleTemplate::query()->orderBy('name')->get();
        return view('admin.sacco_role_templates.index', compact('templates'));
    }

    public function show(GroupRoleTemplate $template)
    {
        return view('admin.sacco_role_templates.show', compact('template'));
    }

    public function update(Request $request, GroupRoleTemplate $template)
    {
        $validated = $request->validate([
            'permissions_text' => 'required|string',
            'description' => 'nullable|string|max:500',
        ]);

        // Split by newlines and filter empty lines
        $perms = array_filter(array_map('trim', explode("\n", $validated['permissions_text'])), fn($p) => !empty($p));

        $template->update([
            'permissions' => array_values($perms),
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->route('admin.sacco_role_templates.index')->with('status', 'Template updated');
    }
}

