<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with('assignees')->orderByDesc('created_at')->paginate(10);
        return view('tasks.index', compact('tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_at' => 'nullable|date',
        ]);

        $validated['creator_id'] = Auth::id();
        $task = Task::create($validated);
        return redirect()->route('tasks.index')->with('status', 'Task created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        $task->load('assignees','dependencies');
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:todo,in_progress,blocked,done',
            'priority' => 'required|in:low,medium,high,urgent',
            'due_at' => 'nullable|date',
        ]);
        $task->update($validated);
        return redirect()->route('tasks.show', $task)->with('status', 'Task updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('status', 'Task deleted');
    }

    public function assign(Request $request, Task $task)
    {
        $this->authorize('update', $task);
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'User not found']);
        }
        $task->assignees()->syncWithoutDetaching([$user->id]);
        return back()->with('status', 'Assignee added');
    }

    public function unassign(Request $request, Task $task, User $user)
    {
        $this->authorize('update', $task);
        $task->assignees()->detach($user->id);
        return back()->with('status', 'Assignee removed');
    }
}
