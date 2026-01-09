<?php

use App\Http\Controllers\Admin\LicenseAdminController;
use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [CustomLoginController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [CustomLoginController::class, 'login']);

Route::post('/logout', [CustomLoginController::class, 'logout'])
    ->name('logout');

Route::middleware(['auth', 'verified'])->group(function () {

    // License management routes (WITHOUT check.license middleware)
    Route::get('/profile/license', [ProfileController::class, 'showLicense'])->name('profile.license');
    Route::get('/profile/get-machine-id', [ProfileController::class, 'getMachineId'])->name('profile.getMachineId');
    Route::post('/profile/license/activate', [ProfileController::class, 'activateLicense'])->name('profile.license.activate');
    Route::post('/profile/license/renew', [ProfileController::class, 'renewLicense'])->name('profile.license.renew');

    // Profile routes (WITHOUT check.license)
    Route::get('/invoice', [InvoiceController::class, 'edit'])->name('invoice.edit');
    Route::get('/invoice/booking/{bookingNumber}', [InvoiceController::class, 'getBookingByNumber']);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'verified', 'check.license'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Room Types Routes
    Route::resource('room-types', RoomTypeController::class);

    // Rooms Routes
    Route::resource('rooms', RoomController::class);
    Route::post('rooms/check-availability', [RoomController::class, 'checkAvailability'])->name('rooms.check-availability');

    // Bookings Routes
    Route::resource('bookings', BookingController::class);
    Route::post('bookings/{booking}/extra-charges', [BookingController::class, 'addExtraCharge'])->name('bookings.extra-charges');
    Route::delete('extra-charges/{charge}', [BookingController::class, 'deleteExtraCharge'])->name('extra-charges.destroy');
    Route::post('bookings/{booking}/payment', [BookingController::class, 'addPayment'])->name('bookings.payment');
    Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    Route::post('bookings/{booking}/check-in', [BookingController::class, 'checkIn'])->name('bookings.check-in');
    Route::post('bookings/{booking}/check-out', [BookingController::class, 'checkOut'])->name('bookings.check-out');
    Route::get('bookings/{booking}/invoice', [BookingController::class, 'invoice'])->name('bookings.invoice');
    Route::DELETE('bookings/{booking_id}', [BookingController::class, 'destroy']);

    // Reports Routes
    Route::get('reports/bookings', [ReportController::class, 'bookings'])->name('reports.bookings');
    Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('reports/tax', [ReportController::class, 'tax'])->name('reports.tax');
    Route::get('reports/occupancy', [ReportController::class, 'occupancy'])->name('reports.occupancy');
    Route::get('reports/export-bookings', [ReportController::class, 'exportBookings'])->name('reports.export-bookings');
    Route::get('reports/export-tax', [ReportController::class, 'exportsTax'])->name('reports.export-tax');

    // Route::resource('profile', ProfileController::class);
    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');
});


Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/license/generate', [LicenseAdminController::class, 'showGenerateForm'])->name('admin.license.generate');
    Route::post('/license/generate', [LicenseAdminController::class, 'generateLicense'])->name('admin.license.store');
});


// require __DIR__.'/auth.php';
