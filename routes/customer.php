<?php

use Illuminate\Support\Facades\Route;

Route::prefix('customer')->name('customer.')->middleware('auth:customer')->group(function () {
    Route::get('dashboard', function () {
        return view('customer.dashboard');
    })->name('dashboard');
});
