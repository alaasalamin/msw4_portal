<?php

use Illuminate\Support\Facades\Route;

Route::prefix('partner')->name('partner.')->middleware('auth:partner')->group(function () {
    Route::get('dashboard', function () {
        return view('partner.dashboard');
    })->name('dashboard');
});
