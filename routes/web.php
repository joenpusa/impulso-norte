<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public Home Route (Redirect logic handled by Controller, now Inertia compatible)
Route::get('/', [\App\Http\Controllers\PublicPageController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Resource Routes (Restored)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('menus', \App\Http\Controllers\Admin\MenuController::class);
        Route::resource('menu-items', \App\Http\Controllers\Admin\MenuItemController::class);
        Route::resource('pages', \App\Http\Controllers\Admin\PageController::class);
        Route::resource('settings', \App\Http\Controllers\Admin\SettingController::class)->only(['index', 'store']);
        Route::resource('media', \App\Http\Controllers\Admin\MediaFileController::class);
    });
});

require __DIR__ . '/auth.php';

// Dynamic Public Page Route
Route::get('/{slug}', [\App\Http\Controllers\PublicPageController::class, 'show'])->name('pages.show');
