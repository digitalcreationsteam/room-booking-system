<?php
// File: app/Http/Controllers/ReportController.php

namespace App\Http\Controllers;

use App\Exports\BookingsExport;
use App\Exports\TaxReportExport;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function bookings(Request $request)
    {
        $query = Booking::with(['bookingRooms.room', 'creator']);

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        if ($request->booking_status) {
            $query->where('booking_status', $request->booking_status);
        }

        if ($request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        $bookings = $query->latest()->get();

        $summary = [
            'total_bookings'    => $bookings->count(),
            'confirmed'         => $bookings->where('booking_status', 'confirmed')->count(),
            'checked_in'        => $bookings->where('booking_status', 'checked_in')->count(),
            'checked_out'       => $bookings->where('booking_status', 'checked_out')->count(),
            'cancelled'         => $bookings->where('booking_status', 'cancelled')->count(),
            'total_revenue'     => $bookings->where('booking_status', '!=', 'cancelled')->sum('total_amount'),
            'advance_collected' => $bookings->sum('advance_payment'),
            'pending_amount'    => $bookings->where('payment_status', '!=', 'paid')
                ->where('booking_status', '!=', 'cancelled')
                ->sum('remaining_amount'),
        ];

        return view('reports.bookings', compact('bookings', 'summary'));
    }

    public function revenue(Request $request)
    {
        // $fromDate = $request->from_date ?? Carbon::now()->startOfMonth();
        // $toDate = $request->to_date ?? Carbon::now()->endOfMonth();
        $fromDate = $request->from_date
            ? Carbon::parse($request->from_date)
            : Carbon::now()->startOfMonth();

        $toDate = $request->to_date
            ? Carbon::parse($request->to_date)
            : Carbon::now()->endOfMonth();

        $bookings = Booking::whereBetween('created_at', [$fromDate, $toDate])
            ->where('booking_status', '!=', 'cancelled')
            ->get();

        $dailyRevenue = $bookings->groupBy(function ($booking) {
            return Carbon::parse($booking->created_at)->format('Y-m-d');
        })->map(function ($dayBookings) {
            return [
                'date'     => $dayBookings->first()->created_at->format('d M Y'),
                'bookings' => $dayBookings->count(),
                'revenue'  => $dayBookings->sum('total_amount'),
                'advance'  => $dayBookings->sum('advance_payment'),
                'pending'  => $dayBookings->sum('remaining_amount'),
            ];
        });

        $summary = [
            'total_revenue'     => $bookings->sum('total_amount'),
            'total_advance'     => $bookings->sum('advance_payment'),
            'total_pending'     => $bookings->sum('remaining_amount'),
            'total_bookings'    => $bookings->count(),
            'avg_booking_value' => $bookings->count() > 0 ? $bookings->sum('total_amount') / $bookings->count() : 0,
        ];

        return view('reports.revenue', compact('dailyRevenue', 'summary', 'fromDate', 'toDate'));
    }

    public function tax(Request $request)
    {
        $fromDate = $request->from_date
            ? Carbon::parse($request->from_date)
            : Carbon::now()->startOfMonth();

        $toDate = $request->to_date
            ? Carbon::parse($request->to_date)
            : Carbon::now()->endOfMonth();

        $bookings = Booking::whereBetween('created_at', [$fromDate, $toDate])
            ->where('booking_status', '!=', 'cancelled')
            ->where('booking_status', '=', 'checked_out') // Exclude checked_out bookings
            ->get();

        $taxSummary = [
            'total_room_charges'  => $bookings->sum('room_charges'),
            'total_gst'           => $bookings->sum('gst_amount'),
            'total_service_tax'   => $bookings->sum('service_tax'),
            'total_other_charges' => $bookings->sum('other_charges'),
            'total_extra_charges' => $bookings->sum('extra_charges'),
            'grand_total'         => $bookings->sum('total_amount'),
        ];

        // GST breakdown by percentage
        $gstBreakdown = $bookings->groupBy(function ($booking) {
            return $booking->bookingRooms->first()->room->gst_percentage ?? 0;
        })->map(function ($group, $gstRate) {
            $totalCharges = $group->sum('room_charges');
            $gstAmount    = $group->sum('gst_amount');
            return [
                'gst_rate'       => $gstRate,
                'taxable_amount' => $totalCharges,
                'gst_amount'     => $gstAmount,
                'bookings_count' => $group->count(),
            ];
        });

        return view('reports.tax', compact('taxSummary', 'gstBreakdown', 'fromDate', 'toDate', 'bookings'));
    }

    // public function exportsTax(Request $request)
    // {
    //     $fromDate = $request->from_date ?? Carbon::now()->startOfMonth();
    //     $toDate   = $request->to_date ?? Carbon::now()->endOfMonth();

    //     $bookings = Booking::with(['bookingRooms.room'])
    //         ->whereBetween('created_at', [$fromDate, $toDate])
    //         ->where('booking_status', '!=', 'cancelled')
    //         ->get();

    //     $filename = 'tax_report_' . date('Y-m-d_His') . '.xlsx';

    //     return Excel::download(new TaxReportExport($bookings), $filename);
    // }

    public function exportsTax(Request $request)
    {
        $fromDate = $request->from_date
            ? Carbon::parse($request->from_date)
            : Carbon::now()->startOfMonth();

        $toDate = $request->to_date
            ? Carbon::parse($request->to_date)
            : Carbon::now()->endOfMonth();

        $bookings = Booking::with(['bookingRooms.room'])
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->where('booking_status', '!=', 'cancelled')
            ->get();

        $filename = 'tax_report_' . now()->format('Y-m-d_His') . '.xlsx';

        return Excel::download(new TaxReportExport($bookings), $filename);
    }
    public function occupancy(Request $request)
    {
        $fromDate = $request->from_date
            ? Carbon::parse($request->from_date)
            : Carbon::now()->startOfMonth();

        $toDate = $request->to_date
            ? Carbon::parse($request->to_date)
            : Carbon::now()->endOfMonth();

        $totalRooms      = Room::count();
        $totalDays       = Carbon::parse($fromDate)->diffInDays(Carbon::parse($toDate)) + 1;
        $totalRoomNights = $totalRooms * $totalDays;

        $bookedNights = Booking::where('booking_status', '!=', 'cancelled')
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('check_in', [$fromDate, $toDate])
                    ->orWhereBetween('check_out', [$fromDate, $toDate])
                    ->orWhere(function ($q) use ($fromDate, $toDate) {
                        $q->where('check_in', '<=', $fromDate)
                            ->where('check_out', '>=', $toDate);
                    });
            })
            ->with('bookingRooms')
            ->get()
            ->sum(function ($booking) use ($fromDate, $toDate) {
                $start  = max(Carbon::parse($booking->check_in), Carbon::parse($fromDate));
                $end    = min(Carbon::parse($booking->check_out), Carbon::parse($toDate));
                $nights = $start->diffInDays($end);
                return $nights * $booking->bookingRooms->count();
            });

        $occupancyRate = $totalRoomNights > 0 ? ($bookedNights / $totalRoomNights) * 100 : 0;

        $roomTypeOccupancy = Room::with(['roomType', 'bookingRooms' => function ($query) use ($fromDate, $toDate) {
            $query->whereHas('booking', function ($q) use ($fromDate, $toDate) {
                $q->where('booking_status', '!=', 'cancelled')
                    ->where(function ($query) use ($fromDate, $toDate) {
                        $query->whereBetween('check_in', [$fromDate, $toDate])
                            ->orWhereBetween('check_out', [$fromDate, $toDate]);
                    });
            });
        }])->get()->groupBy('room_type_id')->map(function ($rooms) use ($totalDays) {
            $totalRooms   = $rooms->count();
            $totalNights  = $totalRooms * $totalDays;
            $bookedNights = $rooms->sum(function ($room) {
                return $room->bookingRooms->sum('booking.number_of_nights');
            });

            return [
                'type_name'      => $rooms->first()->roomType->name,
                'total_rooms'    => $totalRooms,
                'booked_nights'  => $bookedNights,
                'occupancy_rate' => $totalNights > 0 ? ($bookedNights / $totalNights) * 100 : 0,
            ];
        });

        return view('reports.occupancy', compact('occupancyRate', 'totalRooms', 'totalDays',
            'bookedNights', 'totalRoomNights', 'roomTypeOccupancy', 'fromDate', 'toDate'));
    }

    public function exportBookings(Request $request)
    {
        $query = Booking::with(['bookingRooms.room']);

        if ($request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $bookings = $query->get();

        $filename = 'bookings_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(new BookingsExport($bookings), $filename);
    }
}
