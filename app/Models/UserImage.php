<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class UserImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'filename',
        'path',
        'size',
        'mime_type',
        'is_favorite'
    ];

    public function getUrlAttribute()
    {
        return Storage::url($this->path);
    }

    public function isFavorited()
    {
        return Favorite::where('image_type', 'user')
            ->where('image_id', $this->id)
            ->exists();
    }
}
