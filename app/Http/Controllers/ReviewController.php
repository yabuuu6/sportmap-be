<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\SportsField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $sportsFieldId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $field = SportsField::findOrFail($sportsFieldId);

        $review = new Review();
        $review->user_id = Auth::id();
        $review->sports_field_id = $field->id;
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->save();

        return response()->json([
            'status_code' => 201,
            'message' => 'Review added successfully',
            'data' => $review
        ], 201);
    }

    public function index($sportsFieldId)
    {
        $reviews = Review::where('sports_field_id', $sportsFieldId)->with('user')->latest()->get();

        return response()->json([
            'status_code' => 200,
            'message' => 'Reviews retrieved',
            'data' => $reviews
        ]);
    }
} 
