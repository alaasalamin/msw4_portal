<?php

use Illuminate\Support\Facades\Route;

Route::prefix('employee')->name('employee.')->middleware('auth:employee')->group(function () {
    Route::get('dashboard', function () {
        return view('employee.dashboard');
    })->name('dashboard');
});
