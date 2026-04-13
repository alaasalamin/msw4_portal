<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('customer')->name('customer.')->middleware('auth:customer')->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Customer/Dashboard');
    })->name('dashboard');
});
