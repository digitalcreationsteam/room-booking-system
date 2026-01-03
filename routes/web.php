<?php

use App\Http\Controllers\Auth\CustomLoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportController;
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

    // Reports Routes
    Route::get('reports/bookings', [ReportController::class, 'bookings'])->name('reports.bookings');
    Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('reports/tax', [ReportController::class, 'tax'])->name('reports.tax');
    Route::get('reports/occupancy', [ReportController::class, 'occupancy'])->name('reports.occupancy');
    Route::get('reports/export-bookings', [ReportController::class, 'exportBookings'])->name('reports.export-bookings');
    Route::get('reports/export-tax', [ReportController::class, 'exportsTax'])->name('reports.export-tax');

    // Route::resource('profile', ProfileController::class);

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/customers/search', [CustomerController::class, 'search'])->name('customers.search');

});

// require __DIR__.'/auth.php';
