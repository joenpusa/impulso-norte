<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public Home Route (Redirect logic handled by Controller, now Inertia compatible)
Route::get('/', [\App\Http\Controllers\PublicPageController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard', [
        'counts' => [
            'media' => \App\Models\MediaFile::count(),
            'pages' => \App\Models\Page::count(),
            'registros' => \App\Models\RegistroFormulario::count(),
        ]
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Resource Routes (Restored)
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::post('menus/{menu}/reorder', [\App\Http\Controllers\Admin\MenuController::class, 'reorder'])->name('menus.reorder');
        Route::resource('menus', \App\Http\Controllers\Admin\MenuController::class);
        Route::resource('menu-items', \App\Http\Controllers\Admin\MenuItemController::class);
        Route::resource('pages', \App\Http\Controllers\Admin\PageController::class);
        Route::resource('settings', \App\Http\Controllers\Admin\SettingController::class)->only(['index', 'store']);
        Route::resource('media', \App\Http\Controllers\Admin\MediaFileController::class);

        // Registros Formulario Routes
        Route::get('registros', [\App\Http\Controllers\Admin\RegistroFormularioController::class, 'index'])->name('registros.index');
        Route::delete('registros/{registro}', [\App\Http\Controllers\Admin\RegistroFormularioController::class, 'destroy'])->name('registros.destroy');
        Route::get('registros/settings', [\App\Http\Controllers\Admin\RegistroFormularioController::class, 'settings'])->name('registros.settings');
        Route::post('registros/settings', [\App\Http\Controllers\Admin\RegistroFormularioController::class, 'updateSettings'])->name('registros.settings.update');
    });
});

require __DIR__ . '/auth.php';

// Public Form Route
Route::get('/formulario-registro', [\App\Http\Controllers\PublicFormController::class, 'index'])->name('form.index');
Route::post('/formulario-registro', [\App\Http\Controllers\PublicFormController::class, 'store'])->name('form.store');

// Dynamic Public Page Route
Route::get('/{slug}', [\App\Http\Controllers\PublicPageController::class, 'show'])->name('pages.show');
