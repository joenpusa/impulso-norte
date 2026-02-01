<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PageController;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/{slug}', [PageController::class, 'show'])->name('page.show');
