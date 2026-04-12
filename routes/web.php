<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShipmentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('shipments', ShipmentController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('/shipments/{shipment}/track', [ShipmentController::class, 'track'])->name('shipments.track');

    // Technician
    Route::get('/technician/board', [DeviceController::class, 'technicianBoard'])->name('technician.board');
    Route::patch('/devices/{device}/status', [DeviceController::class, 'updateStatus'])->name('devices.status');
    Route::patch('/devices/{device}/notes', [DeviceController::class, 'updateNotes'])->name('devices.notes');
});

require __DIR__.'/auth.php';
