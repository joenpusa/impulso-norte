<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('layouts.main', function ($view) {
            if (\Illuminate\Support\Facades\Schema::hasTable('menus')) {
                $mainMenu = \App\Models\Menu::where('location', 'header')->where('is_active', true)->with('items.children')->first();
                $footerMenu = \App\Models\Menu::where('location', 'footer')->where('is_active', true)->with('items.children')->first();
                $view->with('mainMenu', $mainMenu)->with('footerMenu', $footerMenu);
            }
        });
    }
}
