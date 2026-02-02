<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Setting;

class PublicPageController extends Controller
{
    private function getSharedProps()
    {
        $settings = Setting::pluck('value', 'key')->toArray();
        $mainMenu = Menu::where('location', 'header')->where('is_active', true)->with('items')->first();

        return [
            'settings' => $settings,
            'mainMenu' => $mainMenu,
        ];
    }

    public function index()
    {
        // Try to find the header menu to determine the home page
        $menu = Menu::where('location', 'header')->where('is_active', true)->with([
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

            // If item has a custom URL
            if ($firstItem->url) {
                return redirect($firstItem->url);
            }
        }

        // Fallback: Show welcome page
        // We can reuse the Dynamic Page view or a specific Welcome view
        // For simplicity, let's assume we render a default Welcome if no hierarchy exists.
        return Inertia::render('Welcome', $this->getSharedProps());
    }

    public function show($slug)
    {
        $page = Page::where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return Inertia::render('Frontend/Page', [
            'page' => $page,
            ...$this->getSharedProps(),
        ]);
    }
}
