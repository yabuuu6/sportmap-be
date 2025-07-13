<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\SportsField;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Simpan atau update review untuk sebuah lapangan oleh user login.
     */
    public function store(Request $request, SportsField $sportsField)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $user = auth()->user();

        $review = Review::updateOrCreate(
            [
                'user_id' => $user->id,
                'sports_field_id' => $sportsField->id,
            ],
            [
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]
        );

        $sportsField->rating = $sportsField->reviews()->avg('rating');
        $sportsField->save();

        return response()->json([
            'status_code' => 201,
            'message' => 'Review disimpan',
            'data' => $review,
        ]);
    }




    /**
     * Ambil semua review untuk lapangan tertentu.
     */
    public function index($sportsFieldId)
    {
        $reviews = Review::where('sports_field_id', $sportsFieldId)
            ->with('user') // Ambil data user yang memberi review
            ->latest()
            ->get();

        return response()->json([
            'status_code' => 200,
            'message' => 'Reviews retrieved',
            'data' => $reviews,
        ]);
    } 
}

