<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageElement extends Model
{
    protected $fillable = [
        'page_id',
        'type',
        'content',
        'order',
        'settings',
    ];

    protected $casts = [
        'content' => 'json',
        'settings' => 'json',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
