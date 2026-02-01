<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaFile extends Model
{
    protected $fillable = [
        'title',
        'path',
        'type',
        'size',
    ];

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->path);
    }

    protected static function booted()
    {
        static::deleting(function ($mediaFile) {
            if ($mediaFile->path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($mediaFile->path);
            }
        });
    }
}
