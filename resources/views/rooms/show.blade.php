<!-- File: resources/views/rooms/show.blade.php -->
@extends('layouts.app')

@section('title', 'Room Details')
@section('header', 'Room Details - Room ' . $room->room_number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Details -->
    <div class="lg:col-span-2">
        <!-- Room Information -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold">Room Information</h3>
                @if($room->status != 'booked')
                    <a href="{{ route('rooms.edit', $room) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Room Number</p>
                    <p class="font-semibold text-xl">{{ $room->room_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Room Type</p>
                    <p class="font-semibold">
                        <a href="{{ route('room-types.show', $room->roomType) }}" class="text-blue-600 hover:text-blue-800">
                            {{ $room->roomType->name }}
                        </a>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Floor Number</p>
                    <p class="font-semibold">{{ $room->floor_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Base Price</p>
                    <p class="font-semibold text-blue-600">₹{{ number_format($room->base_price, 2) }}/night</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Current Status</p>
                    <span class="px-3 py-1 text-sm rounded font-semibold
                        @if($room->status == 'available') bg-green-100 text-green-800
                        @elseif($room->status == 'booked') bg-red-100 text-red-800
                        @elseif($room->status == 'maintenance') bg-yellow-100 text-yellow-800
                        @endif">
                        {{ ucfirst($room->status) }}
                    </span>
                </div>
                <div>
                    <p class="text-sm text-gray-600">GST Percentage</p>
                    <p class="font-semibold">{{ $room->gst_percentage }}%</p>
                </div>
                @if($room->service_tax_percentage > 0)
                    <div>
                        <p class="text-sm text-gray-600">Service Tax</p>
                        <p class="font-semibold">{{ $room->service_tax_percentage }}%</p>
                    </div>
                @endif
                @if($room->other_charges > 0)
                    <div>
                        <p class="text-sm text-gray-600">Other Charges</p>
                        <p class="font-semibold">₹{{ number_format($room->other_charges, 2) }}</p>
                    </div>
                @endif
                @if($room->amenities)
                    <div class="col-span-2">
                        <p class="text-sm text-gray-600 mb-2">Amenities</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(',', $room->amenities) as $amenity)
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded text-sm">
                                    <i class="fas fa-check mr-1"></i>{{ trim($amenity) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Price Breakdown -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Price Breakdown (Per Night)</h3>

            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Base Price:</span>
                    <span class="font-semibold">₹{{ number_format($room->base_price, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">GST ({{ $room->gst_percentage }}%):</span>
                    <span class="font-semibold">₹{{ number_format($room->base_price * $room->gst_percentage / 100, 2) }}</span>
                </div>
                @if($room->service_tax_percentage > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Service Tax ({{ $room->service_tax_percentage }}%):</span>
                        <span class="font-semibold">₹{{ number_format($room->base_price * $room->service_tax_percentage / 100, 2) }}</span>
                    </div>
                @endif
                @if($room->other_charges > 0)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Other Charges:</span>
                        <span class="font-semibold">₹{{ number_format($room->other_charges, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between border-t pt-2 text-lg">
                    <span class="font-bold">Total Price:</span>
                    <span class="font-bold text-blue-600">
                        ₹{{ number_format($room->base_price + ($room->base_price * $room->gst_percentage / 100) + ($room->base_price * $room->service_tax_percentage / 100) + $room->other_charges, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Current/Recent Bookings -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Booking History</h3>

            @if($room->bookingRooms && $room->bookingRooms->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Booking Number</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Guest Name</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Check-in</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Check-out</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($room->bookingRooms->take(10) as $bookingRoom)
                                <tr>
                                    <td class="px-4 py-3">
                                        <span class="font-semibold">{{ $bookingRoom->booking->booking_number }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $bookingRoom->booking->customer_name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $bookingRoom->booking->check_in->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        {{ $bookingRoom->booking->check_out->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 text-xs rounded
                                            @if($bookingRoom->booking->booking_status == 'confirmed') bg-blue-100 text-blue-800
                                            @elseif($bookingRoom->booking->booking_status == 'checked_in') bg-green-100 text-green-800
                                            @elseif($bookingRoom->booking->booking_status == 'checked_out') bg-gray-100 text-gray-800
                                            @elseif($bookingRoom->booking->booking_status == 'cancelled') bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $bookingRoom->booking->booking_status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('bookings.show', $bookingRoom->booking) }}"
                                            class="text-blue-600 hover:text-blue-800" title="View Booking">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No booking history available</p>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>

            <div class="space-y-2">
                <a href="{{ route('rooms.index') }}"
                    class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center px-4 py-2 rounded">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Rooms
                </a>

                @if($room->status != 'booked')
                    <a href="{{ route('rooms.edit', $room) }}"
                        class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded">
                        <i class="fas fa-edit mr-2"></i> Edit Room
                    </a>
                @endif

                @if($room->status == 'available')
                    <a href="{{ route('bookings.create', ['room_id' => $room->id]) }}"
                        class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 rounded">
                        <i class="fas fa-calendar-plus mr-2"></i> Create Booking
                    </a>
                @endif

                @if($room->status == 'available')
                    <form action="{{ route('rooms.update', $room) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="maintenance">
                        <button type="submit"
                            class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded">
                            <i class="fas fa-tools mr-2"></i> Mark for Maintenance
                        </button>
                    </form>
                @endif

                @if($room->status == 'maintenance')
                    <form action="{{ route('rooms.update', $room) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="status" value="available">
                        <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                            <i class="fas fa-check mr-2"></i> Mark as Available
                        </button>
                    </form>
                @endif

                @if($room->status == 'available')
                    <form action="{{ route('rooms.destroy', $room) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this room?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                            <i class="fas fa-trash mr-2"></i> Delete Room
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Room Statistics -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Statistics</h3>

            <div class="space-y-3">
                <div class="flex justify-between items-center pb-3 border-b">
                    <span class="text-gray-600">Total Bookings:</span>
                    <span class="font-bold text-xl text-blue-600">
                        {{ $room->bookingRooms ? $room->bookingRooms->count() : 0 }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Active Bookings:</span>
                    <span class="font-semibold text-green-600">
                        {{ $room->bookingRooms ? $room->bookingRooms->whereIn('booking.booking_status', ['confirmed', 'checked_in'])->count() : 0 }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Completed:</span>
                    <span class="font-semibold text-gray-600">
                        {{ $room->bookingRooms ? $room->bookingRooms->where('booking.booking_status', 'checked_out')->count() : 0 }}
                    </span>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Cancelled:</span>
                    <span class="font-semibold text-red-600">
                        {{ $room->bookingRooms ? $room->bookingRooms->where('booking.booking_status', 'cancelled')->count() : 0 }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Status Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Status Information</h3>

            <div class="space-y-3">
                <div class="p-4 rounded
                    @if($room->status == 'available') bg-green-50 border border-green-200
                    @elseif($room->status == 'booked') bg-red-50 border border-red-200
                    @elseif($room->status == 'maintenance') bg-yellow-50 border border-yellow-200
                    @endif">
                    <p class="font-semibold mb-1
                        @if($room->status == 'available') text-green-800
                        @elseif($room->status == 'booked') text-red-800
                        @elseif($room->status == 'maintenance') text-yellow-800
                        @endif">
                        {{ ucfirst($room->status) }}
                    </p>
                    <p class="text-sm
                        @if($room->status == 'available') text-green-600
                        @elseif($room->status == 'booked') text-red-600
                        @elseif($room->status == 'maintenance') text-yellow-600
                        @endif">
                        @if($room->status == 'available')
                            This room is ready for new bookings
                        @elseif($room->status == 'booked')
                            This room is currently occupied
                        @elseif($room->status == 'maintenance')
                            This room is under maintenance
                        @endif
                    </p>
                </div>

                <div class="text-sm text-gray-600">
                    <p><strong>Created:</strong> {{ $room->created_at->format('d M Y, h:i A') }}</p>
                    <p><strong>Last Updated:</strong> {{ $room->updated_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
