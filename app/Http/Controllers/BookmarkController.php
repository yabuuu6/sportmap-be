<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SportsField;
use App\Models\User;


class BookmarkController extends Controller
{
    // Toggle bookmark
    public function toggle($fieldId)
    {
        $user = auth()->user();
        $field = SportsField::findOrFail($fieldId);

        if ($user->bookmarkedFields()->where('sports_field_id', $fieldId)->exists()) {
            $user->bookmarkedFields()->detach($fieldId);
            return response()->json(['message' => 'Bookmark removed']);
        } else {
            $user->bookmarkedFields()->attach($fieldId);
            return response()->json(['message' => 'Bookmarked']);
        }
    }

   public function index()
    {
        $user = auth()->user();
        $bookmarks = $user->bookmarkedFields()->with('reviews')->get();

        return response()->json([
            'status_code' => 200,
            'message' => 'List of bookmarked fields',
            'data' => $bookmarks
        ]);
    }

}
