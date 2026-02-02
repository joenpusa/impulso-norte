<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicPageController extends Controller
{
    public function index()
    {
        // Try to find the header menu
        $menu = \App\Models\Menu::where('location', 'header')->where('is_active', true)->with([
            'items' => function ($query) {
                $query->orderBy('order')->limit(1);
            }
        ])->first();

        // If menu exists and has items
        if ($menu && $menu->items->isNotEmpty()) {
            $firstItem = $menu->items->first();

            // If item links to an internal page, show that page
            if ($firstItem->page_id) {
                return $this->show($firstItem->page->slug);
            }

            // If item has a custom URL and it's internal (starts with / or plain string), redirect?
            // User requirement: "load the first page". If it's a URL, we might redirect.
            if ($firstItem->url) {
                return redirect($firstItem->url);
            }
        }

        // Fallback: Show welcome page using public layout
        return view('welcome');
    }

    public function show($slug)
    {
        $page = \App\Models\Page::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return view('pages.show', compact('page'));
    }
}
