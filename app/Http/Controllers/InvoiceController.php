<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hotel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function edit(Request $request): View
    {
        return view('invoice.edit', [
            'user'  => $request->user(),
            'hotel' => $request->user()->hotel
        ]);
    }


    // public function getBookingByNumber($bookingNumber)
    // {
    //     $booking = Booking::with([
    //         'bookingRooms.room',
    //         'customer'
    //     ])
    //         ->where('booking_number', $bookingNumber)   // ✅ IMPORTANT
    //         ->firstOrFail();

    //     return response()->json([
    //         'booking' => [
    //             'booking_number'   => $booking->booking_number,
    //             'customer_name'    => $booking->customer_name,
    //             'customer_mobile'  => $booking->customer_mobile,
    //             'customer_address' => $booking->customer_address,
    //             'gst_number'       => $booking->gst_number,
    //             'check_in'         => $booking->check_in,
    //             'check_out'        => $booking->check_out,
    //             'number_of_nights' => $booking->number_of_nights,
    //             'total_amount'     => $booking->total_amount,
    //             'advance_payment'  => $booking->advance_payment,
    //             'remaining_amount' => $booking->remaining_amount,
    //             'payment_status'   => $booking->payment_status,
    //             'payment_mode'     => $booking->payment_mode,
    //         ],
    //         'rooms' => $booking->bookingRooms->map(function ($br) {
    //             return [
    //                 'room_no'  => $br->room->room_number,
    //                 'type'     => $br->room->room_type,
    //                 'rate'     => $br->room_price,
    //             ];
    //         })
    //     ]);
    // }

    public function getBookingByNumber($bookingNumber)
{
    $booking = Booking::with(['rooms.roomType', 'bookingRooms.room.roomType'])
        ->where('booking_number', $bookingNumber)
        ->first();

    if (!$booking) {
        return response()->json([
            'success' => false,
            'message' => 'Booking not found'
        ], 404);
    }

    return response()->json([
        'success' => true,
        'booking' => [
            'customer_name' => $booking->customer_name,
            'customer_mobile' => $booking->customer_mobile,
            'customer_address' => $booking->customer_address,
            'gst_number' => $booking->gst_number,
            'booking_number' => $booking->booking_number,
            'check_in' => $booking->check_in,
            'check_out' => $booking->check_out,
            'payment_mode' => $booking->payment_mode,
            'discount_amount' => $booking->discount_amount ?? 0,
            'gst_percentage' => $booking->gst_percentage ?? 0,
            'advance_payment' => $booking->advance_payment ?? 0,
            'rooms' => $booking->bookingRooms->map(function($br) {
                return [
                    'room_number' => $br->room->room_number,
                    'room_type' => $br->room->roomType->name,
                    'room_price' => $br->room_price
                ];
            })
        ]
    ]);
}


}
