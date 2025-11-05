<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignHubController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $status = (string) $request->query('status', 'active');
        $tag = (string) $request->query('tag', '');

        $query = Campaign::query()->orderByDesc('id');
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%{$q}%")
                  ->orWhere('description', 'like', "%{$q}%");
            });
        }
        if ($status !== '') {
            $query->where('status', $status);
        }
        if ($tag !== '') {
            $query->whereJsonContains('tags', $tag);
        }

        $campaigns = $query->paginateDefault();

        // Suggested tags (top 20) - naive aggregation
        $tags = Campaign::query()
            ->whereNotNull('tags')
            ->select('tags')
            ->limit(200)
            ->get()
            ->flatMap(fn($c) => (array) $c->tags)
            ->filter()
            ->countBy()
            ->sortDesc()
            ->take(20)
            ->keys()
            ->values();

        return view('campaigns.hub', compact('campaigns', 'q', 'status', 'tag', 'tags'));
    }
}


