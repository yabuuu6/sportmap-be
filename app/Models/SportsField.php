<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SportsField extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'type',
        'image_path',
    ];
    
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

}