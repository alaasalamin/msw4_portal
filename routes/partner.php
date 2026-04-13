<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('partner')->name('partner.')->middleware('auth:partner')->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Partner/Dashboard');
    })->name('dashboard');
});
