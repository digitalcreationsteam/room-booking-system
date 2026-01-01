@extends('layouts.app')

@section('title', 'Create Booking')
@section('header', 'Create New Booking')

@section('content')
<form action="{{ route('bookings.store') }}" method="POST">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Details -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Customer Details</h3>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Customer Name *</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Mobile Number *</label>
                        <input type="text" name="customer_mobile" value="{{ old('customer_mobile') }}"
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Email</label>
                    <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                        class="w-full px-3 py-2 border rounded">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Address *</label>
                    <textarea name="customer_address" rows="2"
                        class="w-full px-3 py-2 border rounded" required>{{ old('customer_address') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">ID Proof Type</label>
                        <select name="id_proof_type" class="w-full px-3 py-2 border rounded">
                            <option value="">Select</option>
                            <option value="aadhar">Aadhar Card</option>
                            <option value="pan">PAN Card</option>
                            <option value="driving_license">Driving License</option>
                            <option value="passport">Passport</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">ID Proof Number</label>
                        <input type="text" name="id_proof_number" value="{{ old('id_proof_number') }}"
                            class="w-full px-3 py-2 border rounded">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Company Name</label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}"
                            class="w-full px-3 py-2 border rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">GST Number</label>
                        <input type="text" name="gst_number" value="{{ old('gst_number') }}"
                            class="w-full px-3 py-2 border rounded">
                    </div>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Booking Details</h3>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Check-in Date *</label>
                        <input type="datetime-local" name="check_in" value="{{ old('check_in') }}"
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Check-out Date *</label>
                        <input type="datetime-local" name="check_out" value="{{ old('check_out') }}"
                            class="w-full px-3 py-2 border rounded" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Number of Adults *</label>
                        <input type="number" name="number_of_adults" value="{{ old('number_of_adults', 1) }}"
                            class="w-full px-3 py-2 border rounded" required min="1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Number of Children *</label>
                        <input type="number" name="number_of_children" value="{{ old('number_of_children', 0) }}"
                            class="w-full px-3 py-2 border rounded" min="0">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-2">Select Rooms *</label>
                    <div class="grid grid-cols-2 gap-4 max-h-64 overflow-y-auto border rounded p-4">
                        @foreach($rooms as $room)
                            <label class="flex items-center p-3 border rounded hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="room_ids[]" value="{{ $room->id }}" class="mr-3">
                                <div class="flex-1">
                                    <div class="font-semibold">Room {{ $room->room_number }}</div>
                                    <div class="text-sm text-gray-600">{{ $room->roomType->name }}</div>
                                    <div class="text-sm font-medium text-blue-600">₹{{ number_format($room->base_price, 2) }}/night</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        <div>
            <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                <h3 class="text-lg font-semibold mb-4">Payment Details</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Advance Payment (₹)</label>
                    <input type="number" name="advance_payment" value="{{ old('advance_payment', 0) }}"
                        step="0.01" class="w-full px-3 py-2 border rounded">
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1">Payment Mode</label>
                    <select name="payment_mode" class="w-full px-3 py-2 border rounded">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="upi">UPI</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>

                <div class="border-t pt-4">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 px-6 py-3 rounded font-semibold">
                        <i class="fas fa-check mr-2"></i> Create Booking
                    </button>
                    <a href="{{ route('bookings.index') }}" class="block text-center mt-3 text-gray-600 hover:text-gray-800">
                        Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
