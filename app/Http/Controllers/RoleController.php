<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\SportsField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        // Cek lapangan
        $field = SportsField::findOrFail($id);

        // Simpan review
        $review = new Review([
            'user_id' => auth()->id(), // pastikan pakai auth
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        $field->reviews()->save($review);

        return response()->json([
            'status_code' => 201,
            'message' => 'Review berhasil ditambahkan',
            'data' => $review
        ]);
    }
}

