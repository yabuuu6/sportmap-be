<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Review;

class SportsField extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'type',
        'image_path',
        'latitude',
        'longitude',
        'is_verified',
        'rating',
    ];

    protected $appends = ['average_rating', 'is_bookmarked'];

    protected $casts = [
        'latitude' => 'double',
        'longitude' => 'double',
        'rating' => 'double',
        'is_verified' => 'boolean',
    ];

    public function reviews()
    {
        return $this->hasMany(Review::class, 'sports_field_id');
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating') ?? 0.0, 1);
    }

    public function bookmarkedByUsers()
    {
        return $this->belongsToMany(User::class, 'bookmarks')->withTimestamps();
    }

    public function getIsBookmarkedAttribute()
    {
        try {
            $user = auth()->user();
            if (!$user) return false;

            return $this->bookmarkedByUsers()->where('user_id', $user->id)->exists();
        } catch (\Throwable $e) {
            \Log::error('Error in getIsBookmarkedAttribute: ' . $e->getMessage());
            return false;
        }
    }
}
