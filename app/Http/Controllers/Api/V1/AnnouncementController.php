<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementReadState;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * List all published announcements for the current user
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Announcement::query()
            ->where(function ($q) {
                $q->whereNull('scheduled_at')
                    ->orWhere('scheduled_at', '<=', now());
            })
            ->where(function ($q) {
                $q->where('status', 'published')
                    ->orWhere('is_published', true);
            });

        // Filter by target audience
        if (!$user || !$user->hasActivePremium()) {
            $query->where('target_audience', 'all');
        }

        $announcements = $query->latest()
            ->paginate(20);

        // Map read state
        $announcements->getCollection()->transform(function ($announcement) use ($user) {
            $announcement->is_read = $user ? $announcement->readByUsers()->where('user_id', $user->id)->exists() : false;
            return $announcement;
        });

        return response()->json($announcements);
    }

    /**
     * Show a single announcement
     */
    public function show($id, Request $request)
    {
        $announcement = Announcement::findOrFail($id);
        $user = $request->user();

        // Check if user can see it
        if ($announcement->target_audience === 'premium' && (!$user || !$user->hasActivePremium())) {
            return response()->json(['message' => 'This announcement is for premium members only.'], 403);
        }

        $announcement->is_read = $user ? $announcement->readByUsers()->where('user_id', $user->id)->exists() : false;

        return response()->json($announcement);
    }

    /**
     * Mark an announcement as read
     */
    public function markRead($id, Request $request)
    {
        $user = $request->user();
        if (!$user)
            return response()->json(['message' => 'Unauthorized'], 401);

        $announcement = Announcement::findOrFail($id);

        AnnouncementReadState::updateOrCreate(
            ['user_id' => $user->id, 'announcement_id' => $announcement->id],
            ['read_at' => now()]
        );

        return response()->json(['message' => 'Marked as read']);
    }
}
