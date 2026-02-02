<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [\App\Http\Controllers\PublicPageController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('menus', \App\Http\Controllers\Admin\MenuController::class);
        Route::resource('menu-items', \App\Http\Controllers\Admin\MenuItemController::class);
        Route::resource('pages', \App\Http\Controllers\Admin\PageController::class);
        Route::resource('settings', \App\Http\Controllers\Admin\SettingController::class)->only(['index', 'store']);
        Route::resource('media', \App\Http\Controllers\Admin\MediaFileController::class);
    });
});

require __DIR__ . '/auth.php';

Route::get('/{slug}', [\App\Http\Controllers\PublicPageController::class, 'show'])->name('pages.show');
