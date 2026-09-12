<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MenuItemController;
use App\Http\Controllers\PrivacyController;
use App\Http\Controllers\RobotsController;
use Illuminate\Support\Facades\Route;

Route::get('/robots.txt', RobotsController::class)->name('robots');
Route::get('/', HomeController::class)->name('home');
Route::get('/menu', MenuController::class)->name('menu');
Route::get('/menu/{category:slug}', MenuController::class)->name('menu.category');
Route::get('/menu/{category:slug}/{menuItem:slug}', MenuItemController::class)
    ->name('menu.show')
    ->scopeBindings();
Route::get('/privacy', PrivacyController::class)->name('privacy');
