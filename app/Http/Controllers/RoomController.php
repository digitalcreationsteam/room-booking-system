<?php
// File: app/Http/Controllers/RoomController.php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Booking;


class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('roomType')->latest()->get();
        return view('rooms.index', compact('rooms'));
    }

    public function create()
    {
        $roomTypes = RoomType::where('is_active', true)->get();
        return view('rooms.create', compact('roomTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number',
            'room_type_id' => 'required|exists:room_types,id',
            'floor_number' => 'required|integer|min:0',
            'base_price' => 'required|numeric|min:0',
            'gst_percentage' => 'required|numeric|min:0|max:100',
            'service_tax_percentage' => 'nullable|numeric|min:0|max:100',
            'other_charges' => 'nullable|numeric|min:0',
            'amenities' => 'nullable|string',
            'status' => 'required|in:available,booked,maintenance'
        ]);

        Room::create($validated);

        return redirect()->route('rooms.index')
            ->with('success', 'Room created successfully!');
    }

    public function show(Room $room)
    {
        $room->load(['roomType', 'bookingRooms.booking']);
        return view('rooms.show', compact('room'));
    }

    public function edit(Room $room)
    {
        $roomTypes = RoomType::where('is_active', true)->get();
        return view('rooms.edit', compact('room', 'roomTypes'));
    }

    // public function update(Request $request, Room $room)
    // {
    //     $validated = $request->validate([
    //         'room_number' => 'required|string|unique:rooms,room_number,' . $room->id,
    //         'room_type_id' => 'required|exists:room_types,id',
    //         'floor_number' => 'required|integer|min:0',
    //         'base_price' => 'required|numeric|min:0',
    //         'gst_percentage' => 'required|numeric|min:0|max:100',
    //         'service_tax_percentage' => 'nullable|numeric|min:0|max:100',
    //         'other_charges' => 'nullable|numeric|min:0',
    //         'amenities' => 'nullable|string',
    //         'status' => 'required|in:available,booked,maintenance'
    //     ]);

    //     $room->update($validated);

    //     return redirect()->route('rooms.index')
    //         ->with('success', 'Room updated successfully!');
    // }




    public function update(Request $request, Booking $booking)
    {
        // ❌ Cancelled booking protect
        if ($booking->booking_status === 'cancelled') {
            return redirect()
                ->route('bookings.show', $booking->id)
                ->with('error', 'Cancelled booking cannot be updated.');
        }

        // ✅ Validation
        $validated = $request->validate([
            'customer_name'       => 'required|string|max:255',
            'customer_mobile'     => 'required|string|max:20',
            'customer_email'      => 'nullable|email',
            'customer_address'    => 'required|string',
            'company_name'        => 'nullable|string|max:255',
            'gst_number'          => 'nullable|string|max:20',

            'number_of_adults'    => 'required|integer|min:1',
            'number_of_children' => 'nullable|integer|min:0',

            // 💰 Payment editable fields
            'room_charges'        => 'required|numeric|min:0',
            'gst_amount'          => 'required|numeric|min:0',
            'service_tax'         => 'nullable|numeric|min:0',
            'other_charges'       => 'nullable|numeric|min:0',

            'total_amount'        => 'required|numeric|min:0',
            'advance_payment'     => 'nullable|numeric|min:0',
            'remaining_amount'    => 'required|numeric|min:0',

            'payment_status'      => 'required|in:pending,partial,paid',
        ]);

        // 🧮 Safe defaults
        $advancePayment = $validated['advance_payment'] ?? 0;
        $serviceTax     = $validated['service_tax'] ?? 0;
        $otherCharges   = $validated['other_charges'] ?? 0;

        // 🔁 Auto-fix payment status (extra safety)
        $paymentStatus = $validated['payment_status'];

        if ($advancePayment >= $validated['total_amount']) {
            $paymentStatus = 'paid';
        } elseif ($advancePayment > 0) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'pending';
        }

        // ✅ Update booking
        $booking->update([
            'customer_name'       => $validated['customer_name'],
            'customer_mobile'     => $validated['customer_mobile'],
            'customer_email'      => $validated['customer_email'],
            'customer_address'    => $validated['customer_address'],
            'company_name'        => $validated['company_name'],
            'gst_number'          => $validated['gst_number'],

            'number_of_adults'    => $validated['number_of_adults'],
            'number_of_children' => $validated['number_of_children'],

            'room_charges'        => $validated['room_charges'],
            'gst_amount'          => $validated['gst_amount'],
            'service_tax'         => $serviceTax,
            'other_charges'       => $otherCharges,

            'total_amount'        => $validated['total_amount'],
            'advance_payment'     => $advancePayment,
            'remaining_amount'    => $validated['remaining_amount'],

            'payment_status'      => $paymentStatus,
        ]);

        return redirect()
            ->route('bookings.show', $booking->id)
            ->with('success', 'Booking updated successfully!');
    }


    public function destroy(Room $room)
    {
        if ($room->bookingRooms()->whereHas('booking', function ($query) {
            $query->where('booking_status', '!=', 'cancelled');
        })->exists()) {
            return redirect()->route('rooms.index')
                ->with('error', 'Cannot delete room with active bookings!');
        }

        $room->delete();

        return redirect()->route('rooms.index')
            ->with('success', 'Room deleted successfully!');
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'room_type_id' => 'nullable|exists:room_types,id'
        ]);

        $query = Room::where('status', 'available');

        if ($request->room_type_id) {
            $query->where('room_type_id', $request->room_type_id);
        }

        $availableRooms = $query->get()->filter(function ($room) use ($request) {
            return $room->isAvailable($request->check_in, $request->check_out);
        });

        return response()->json([
            'available_rooms' => $availableRooms->values()
        ]);
    }
}
