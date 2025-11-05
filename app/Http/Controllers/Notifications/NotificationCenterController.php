<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationCenterController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $filter = $request->query('filter', 'all'); // all, unread, read, archived
        $type = $request->query('type'); // filter by notification type

        $query = match ($filter) {
            'unread' => $user->unreadNotifications(),
            'read' => $user->readNotifications(),
            'archived' => $user->notifications()->whereNotNull('deleted_at'),
            default => $user->notifications()->whereNull('deleted_at'),
        };

        if ($type) {
            $query->where('type', 'like', '%' . $type . '%');
        }

        $notifications = $query->orderByDesc('created_at')->paginateDefault();

        // Get unique types for filter dropdown
        $types = $user->notifications()
            ->whereNull('deleted_at')
            ->selectRaw('DISTINCT type')
            ->pluck('type')
            ->map(fn($t) => class_basename($t))
            ->unique()
            ->sort()
            ->values();

        return view('notifications.index', compact('notifications', 'filter', 'type', 'types'));
    }

    public function markRead(Request $request, string $id)
    {
        $n = $request->user()->notifications()->where('id', $id)->firstOrFail();
        if ($n->read_at === null) {
            $n->markAsRead();
        }
        return redirect()->back();
    }

    public function markAllRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return redirect()->back();
    }

    public function bulk(Request $request)
    {
        $validated = $request->validate([
            'action' => 'required|in:mark_read,mark_unread,archive,unarchive,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'string'
        ]);

        $user = $request->user();
        $items = $user->notifications()->whereIn('id', $validated['ids'])->get();
        foreach ($items as $n) {
            switch ($validated['action']) {
                case 'mark_read':
                    $n->markAsRead();
                    break;
                case 'mark_unread':
                    $n->read_at = null;
                    $n->save();
                    break;
                case 'archive':
                    $n->delete(); // Soft delete for archive
                    break;
                case 'unarchive':
                    $n->restore();
                    break;
                case 'delete':
                    $n->forceDelete();
                    break;
            }
        }
        return redirect()->back()->with('status', 'Action completed.');
    }
}


