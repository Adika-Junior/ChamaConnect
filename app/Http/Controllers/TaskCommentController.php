<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\TaskComment;

class TaskCommentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $request->validate(['body' => 'required|string']);
        $this->authorize('view', $task);
        TaskComment::create([
            'task_id' => $task->id,
            'user_id' => $request->user()->id,
            'body' => $request->body,
        ]);
        return back();
    }
}
