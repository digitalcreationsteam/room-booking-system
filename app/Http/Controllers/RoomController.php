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
        $rooms = Room::with('roomType')
            ->latest()
            ->paginate(10);
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

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number' => 'required|string|unique:rooms,room_number,' . $room->id,
            'room_type_id' => 'required|exists:room_types,id',
            'floor_number' => 'required|integer|min:0',
            'base_price' => 'required|numeric|min:0',
            'gst_percentage' => 'required|numeric|min:0|max:100',
            'service_tax_percentage' => 'nullable|numeric|min:0|max:100',
            'other_charges' => 'nullable|numeric|min:0',
            'amenities' => 'nullable|string',
            'status' => 'required|in:available,booked,maintenance'
        ]);

        $room->update($validated);

        return redirect()->route('rooms.index')
            ->with('success', 'Room updated successfully!');
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
