<?php

use App\Http\Controllers\Auth\CustomerLoginController;
use App\Http\Controllers\Auth\EmployeeLoginController;
use App\Http\Controllers\Auth\PartnerLoginController;
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
        'canResetPassword' => Route::has('password.request'),
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

// Employee auth routes
Route::prefix('employee')->name('employee.')->group(function () {
    Route::get('/',       fn() => redirect()->route('employee.login'));
    Route::get('login',   [EmployeeLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login',  [EmployeeLoginController::class, 'login']);
    Route::post('logout', [EmployeeLoginController::class, 'logout'])->name('logout');
});

// Customer auth routes
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('login',      [CustomerLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login',     [CustomerLoginController::class, 'login']);
    Route::get('register',   [CustomerLoginController::class, 'showRegisterForm'])->name('register');
    Route::post('register',  [CustomerLoginController::class, 'register']);
    Route::post('logout',    [CustomerLoginController::class, 'logout'])->name('logout');
});

// Partner auth routes
Route::prefix('partner')->name('partner.')->group(function () {
    Route::get('login',      [PartnerLoginController::class, 'showLoginForm'])->name('login');
    Route::post('login',     [PartnerLoginController::class, 'login']);
    Route::get('register',   [PartnerLoginController::class, 'showRegisterForm'])->name('register');
    Route::post('register',  [PartnerLoginController::class, 'register']);
    Route::post('logout',    [PartnerLoginController::class, 'logout'])->name('logout');
});

require __DIR__.'/auth.php';
