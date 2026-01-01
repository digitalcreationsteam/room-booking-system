@extends('layouts.app')

@section('title', 'Edit Booking')
@section('header', 'Edit Booking')

@section('content')

@if($booking->booking_status === 'cancelled')
    <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
        This booking has been cancelled and cannot be edited.
    </div>
@endif

<form action="{{ route('bookings.update', $booking->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- LEFT SECTION -->
        <div class="lg:col-span-2">

            <!-- CUSTOMER DETAILS -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Customer Details</h3>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Customer Name *</label>
                        <input type="text" name="customer_name"
                               value="{{ old('customer_name', $booking->customer_name) }}"
                               class="w-full px-3 py-2 border rounded" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Mobile Number *</label>
                        <input type="text" name="customer_mobile"
                               value="{{ old('customer_mobile', $booking->customer_mobile) }}"
                               class="w-full px-3 py-2 border rounded" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="customer_email"
                           value="{{ old('customer_email', $booking->customer_email) }}"
                           class="w-full px-3 py-2 border rounded">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Address *</label>
                    <textarea name="customer_address" rows="2"
                              class="w-full px-3 py-2 border rounded"
                              required>{{ old('customer_address', $booking->customer_address) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Company Name</label>
                        <input type="text" name="company_name"
                               value="{{ old('company_name', $booking->company_name) }}"
                               class="w-full px-3 py-2 border rounded">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">GST Number</label>
                        <input type="text" name="gst_number"
                               value="{{ old('gst_number', $booking->gst_number) }}"
                               class="w-full px-3 py-2 border rounded">
                    </div>
                </div>
            </div>

            <!-- BOOKING DETAILS -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Booking Details</h3>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Check-in</label>
                        <input type="datetime-local"
                               value="{{ $booking->check_in->format('Y-m-d\TH:i') }}"
                               class="w-full px-3 py-2 border rounded bg-gray-100" disabled>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Check-out</label>
                        <input type="datetime-local"
                               value="{{ $booking->check_out->format('Y-m-d\TH:i') }}"
                               class="w-full px-3 py-2 border rounded bg-gray-100" disabled>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Adults *</label>
                        <input type="number" name="number_of_adults"
                               value="{{ old('number_of_adults', $booking->number_of_adults) }}"
                               class="w-full px-3 py-2 border rounded" min="1" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Children</label>
                        <input type="number" name="number_of_children"
                               value="{{ old('number_of_children', $booking->number_of_children) }}"
                               class="w-full px-3 py-2 border rounded" min="0">
                    </div>
                </div>

                <!-- ROOMS -->
                <div>
                    <label class="block text-sm font-medium mb-2">Booked Rooms</label>
                    <div class="grid grid-cols-2 gap-4 border rounded p-4">
                        @foreach($booking->rooms as $room)
                            <div class="p-3 border rounded bg-gray-50">
                                <div class="font-semibold">Room {{ $room->room_number }}</div>
                                <div class="text-sm text-gray-600">{{ $room->roomType->name }}</div>
                                <div class="text-sm font-medium text-blue-600">
                                    ₹{{ number_format($room->base_price, 2) }}/night
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        Room changes are not allowed after booking creation.
                    </p>
                </div>
            </div>
        </div>

        <!-- RIGHT SECTION : PAYMENT SUMMARY -->
        <div>
            <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                <h3 class="text-lg font-semibold mb-4">Payment Summary</h3>

                <div class="space-y-3 text-sm">

                    <div>
                        <label class="block mb-1">Room Charges</label>
                        <input type="number" step="0.01" name="room_charges"
                               value="{{ old('room_charges', $booking->room_charges) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block mb-1">GST Amount</label>
                        <input type="number" step="0.01" name="gst_amount"
                               value="{{ old('gst_amount', $booking->gst_amount) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block mb-1">Service Tax</label>
                        <input type="number" step="0.01" name="service_tax"
                               value="{{ old('service_tax', $booking->service_tax) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block mb-1">Other Charges</label>
                        <input type="number" step="0.01" name="other_charges"
                               value="{{ old('other_charges', $booking->other_charges) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block mb-1 font-semibold">Total Amount</label>
                        <input type="number" step="0.01" name="total_amount"
                               value="{{ old('total_amount', $booking->total_amount) }}"
                               class="w-full border rounded px-3 py-2 font-semibold">
                    </div>

                    <div>
                        <label class="block mb-1">Advance Paid</label>
                        <input type="number" step="0.01" name="advance_payment"
                               value="{{ old('advance_payment', $booking->advance_payment) }}"
                               class="w-full border rounded px-3 py-2">
                    </div>

                    <div>
                        <label class="block mb-1 text-red-600">Remaining Amount</label>
                        <input type="number" step="0.01" name="remaining_amount"
                               value="{{ old('remaining_amount', $booking->remaining_amount) }}"
                               class="w-full border rounded px-3 py-2 text-red-600 font-semibold">
                    </div>

                    <div>
                        <label class="block mb-1">Payment Status</label>
                        <select name="payment_status" class="w-full border rounded px-3 py-2">
                            <option value="pending" {{ $booking->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="partial" {{ $booking->payment_status == 'partial' ? 'selected' : '' }}>Partial</option>
                            <option value="paid" {{ $booking->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                </div>

                <div class="border-t mt-6 pt-4">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded font-semibold"
                        {{ $booking->booking_status === 'cancelled' ? 'disabled' : '' }}>
                        Update Booking
                    </button>

                    <a href="{{ route('bookings.show', $booking->id) }}"
                       class="block text-center mt-3 text-gray-600 hover:text-gray-800">
                        Cancel
                    </a>
                </div>
            </div>
        </div>

    </div>
</form>
@endsection


