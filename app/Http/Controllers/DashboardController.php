<?php
// File: app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // return 1;
        $today = Carbon::today();

        $stats = [
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('status', 'available')->count(),
            'booked_rooms' => Room::where('status', 'booked')->count(),
            'maintenance_rooms' => Room::where('status', 'maintenance')->count(),
            'todays_checkins' => Booking::whereDate('check_in', $today)
                ->where('booking_status', 'confirmed')
                ->count(),
            'todays_checkouts' => Booking::whereDate('check_out', $today)
                ->whereIn('booking_status', ['confirmed', 'checked_in'])
                ->count(),
            'todays_revenue' => Booking::whereDate('created_at', $today)
                ->sum('total_amount'),
            'pending_payments' => Booking::where('payment_status', '!=', 'paid')
                ->where('booking_status', '!=', 'cancelled')
                ->sum('remaining_amount')
        ];

        $recentBookings = Booking::with(['bookingRooms.room', 'creator'])
            ->latest()
            ->take(10)
            ->get();

        $todaysCheckins = Booking::with(['bookingRooms.room'])
            ->whereDate('check_in', $today)
            ->where('booking_status', 'confirmed')
            ->get();

        $todaysCheckouts = Booking::with(['bookingRooms.room'])
            ->whereDate('check_out', $today)
            ->whereIn('booking_status', ['confirmed', 'checked_in'])
            ->get();

        return view('dashboard', compact('stats', 'recentBookings', 'todaysCheckins', 'todaysCheckouts'));
    }
}
