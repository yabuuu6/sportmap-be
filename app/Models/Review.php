<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'sports_field_id',
        'rating',
        'comment'
    ];

    public function field()
    {
        return $this->belongsTo(SportsField::class, 'sports_field_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}



