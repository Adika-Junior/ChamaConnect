<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivity;
use Illuminate\Http\Request;

class AdminActivityController extends Controller
{
    public function index(Request $request)
    {
        $actor = $request->query('actor');
        $action = $request->query('action');
        $q = AdminActivity::query()->orderByDesc('id');
        if ($actor) $q->where('actor_id', $actor);
        if ($action) $q->where('action', 'like', "%{$action}%");
        $activities = $q->paginateDefault();
        return view('admin.activities.index', compact('activities', 'actor', 'action'));
    }

    public function export(Request $request)
    {
        $actor = $request->query('actor');
        $action = $request->query('action');
        $q = AdminActivity::query()->orderByDesc('id');
        if ($actor) $q->where('actor_id', $actor);
        if ($action) $q->where('action', 'like', "%{$action}%");

        $headers = ['ID','Actor ID','Action','Target Type','Target ID','IP','User Agent','Created At'];
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $headers);
        foreach ($q->cursor() as $a) {
            fputcsv($handle, [
                $a->id, $a->actor_id, $a->action, $a->target_type, $a->target_id, $a->ip, $a->user_agent, $a->created_at,
            ]);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=admin_activities_'.date('Ymd_His').'.csv',
        ]);
    }
}


