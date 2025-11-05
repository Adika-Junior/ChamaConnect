<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Task;
use App\Models\TaskAttachment;

class TaskAttachmentController extends Controller
{
    public function store(Request $request, Task $task)
    {
        $this->authorize('view', $task);
        $request->validate(['file' => 'required|file|max:20480']);
        $file = $request->file('file');
        $path = $file->store('attachments', 'public');
        TaskAttachment::create([
            'task_id' => $task->id,
            'uploaded_by' => $request->user()->id,
            'filename' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size_bytes' => $file->getSize(),
        ]);
        return back();
    }
}
