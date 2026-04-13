<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::prefix('employee')->name('employee.')->middleware('auth:employee')->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('Employee/Dashboard');
    })->name('dashboard');
});
