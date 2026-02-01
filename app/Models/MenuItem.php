<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id',
        'parent_id',
        'title',
        'url',
        'page_id',
        'order',
        'target',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    protected static function booted()
    {
        static::saving(function ($item) {
            if ($item->parent_id && !$item->menu_id) {
                $item->menu_id = MenuItem::find($item->parent_id)?->menu_id;
            }
        });
    }
}
