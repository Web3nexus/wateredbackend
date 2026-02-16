<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Http\Resources\BookmarkResource;
use Illuminate\Http\Request;

class BookmarkController extends Controller
{
    public function index(Request $request)
    {
        $bookmarks = $request->user()->bookmarks()->with('bookmarkable')->latest()->get();
        return BookmarkResource::collection($bookmarks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'bookmarkable_id' => 'required|integer',
            'bookmarkable_type' => 'required|string',
        ]);

        // Map short names to full Model classes if necessary, 
        // OR rely on Frontend sending full class names / MorphMap aliases.
        // For security, checking class existence is a good idea.

        $bookmark = $request->user()->bookmarks()->firstOrCreate([
            'bookmarkable_id' => $request->bookmarkable_id,
            'bookmarkable_type' => $request->bookmarkable_type,
        ]);

        return response()->json($bookmark, 201);
    }

    public function destroy(Bookmark $bookmark)
    {
        if ($bookmark->user_id !== request()->user()->id) {
            abort(403);
        }

        $bookmark->delete();

        return response()->noContent();
    }

    public function destroyByItem(Request $request)
    {
        $request->validate([
            'bookmarkable_id' => 'required|integer',
            'bookmarkable_type' => 'required|string',
        ]);

        $request->user()->bookmarks()
            ->where('bookmarkable_id', $request->bookmarkable_id)
            ->where('bookmarkable_type', $request->bookmarkable_type)
            ->delete();

        return response()->noContent();
    }
}
