<?php

use App\Http\Controllers\Auth\CustomerLoginController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Auth\EmployeeLoginController;
use App\Http\Controllers\Auth\PartnerLoginController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceNoteController;
use App\Http\Controllers\DevicePhotoUploadController;
use App\Http\Controllers\FormSubmissionController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShipmentController;
use App\Models\Setting;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return Inertia::render('Welcome', [
        'canLogin'         => Route::has('login'),
        'canRegister'      => Route::has('register'),
        'canResetPassword' => Route::has('password.request'),
        'homepage'         => \App\Http\Controllers\HomepageController::content(),
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Broadcasting auth for the admin/employee panel (uses 'admin' guard)
Route::post('/admin/broadcasting/auth', function (\Illuminate\Http\Request $request) {
    return \Illuminate\Support\Facades\Broadcast::auth($request);
})->middleware(['web', 'auth:admin'])->name('admin.broadcasting.auth');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Customer routes
    Route::get('/shipments', [ShipmentController::class, 'index'])->name('shipments.index');
    Route::get('/shipments/{shipment}', [ShipmentController::class, 'show'])->name('shipments.show');
    Route::get('/shipments/create', [ShipmentController::class, 'create'])->middleware('type.can:submit_repair')->name('shipments.create');
    Route::post('/shipments', [ShipmentController::class, 'store'])->middleware('type.can:submit_repair')->name('shipments.store');
    Route::get('/shipments/{shipment}/track', [ShipmentController::class, 'track'])->middleware('type.can:track_shipments')->name('shipments.track');

    // Technician (employee)
    Route::get('/technician/board', [DeviceController::class, 'technicianBoard'])->middleware('type.can:technician_board')->name('technician.board');
    Route::patch('/devices/{device}/status', [DeviceController::class, 'updateStatus'])->middleware('type.can:update_repair_status')->name('devices.status');
    Route::patch('/devices/{device}/notes', [DeviceController::class, 'updateNotes'])->middleware('type.can:add_repair_notes')->name('devices.notes');
});

// One-time photo upload (public, no auth — token is the key)
Route::get('/upload/{token}',  [DevicePhotoUploadController::class, 'show'])->name('device-photo.show');
Route::post('/upload/{token}', [DevicePhotoUploadController::class, 'store'])->name('device-photo.store');

// Device notes — accessible from admin panel (auth:admin) or user portals (auth:web)
Route::post('/devices/{device}/notes',             [DeviceNoteController::class, 'store'])->name('device-notes.store');
Route::patch('/device-notes/{note}',               [DeviceNoteController::class, 'update'])->name('device-notes.update');
Route::delete('/device-notes/{note}',              [DeviceNoteController::class, 'destroy'])->name('device-notes.destroy');

// Employee auth routes
Route::prefix('employee')->name('employee.')->group(function () {
    Route::get('/',       fn() => redirect('/admin/login'));
    Route::get('login',   fn() => redirect('/admin/login'))->name('login');
    Route::post('login',  fn() => redirect('/admin/login'));
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

// Public blog (order matters: static segments first)
Route::get('/blog',                     [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/category/{category}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/blog/{category}/{slug}',   [BlogController::class, 'show'])->name('blog.show');
Route::get('/blog/{slug}',              [BlogController::class, 'showLegacy'])->name('blog.show.legacy');

// Form submissions (public, no auth required)
Route::post('/forms/{form}/submit', [FormSubmissionController::class, 'submit'])->name('forms.submit');

// Dynamic CMS pages — must be last to avoid shadowing named routes
Route::get('/{slug}', [PageController::class, 'show'])
    ->name('page.show')
    ->where('slug', '[a-z0-9][a-z0-9\-]*');

require __DIR__.'/auth.php';
