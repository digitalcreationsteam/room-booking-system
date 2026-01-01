<!-- File: resources/views/bookings/show.blade.php -->
@extends('layouts.app')

@section('title', 'Booking Details')
@section('header', 'Booking Details - ' . $booking->booking_number)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Details -->
    <div class="lg:col-span-2">
        <!-- Customer Info -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-start mb-4">
                <h3 class="text-lg font-semibold">Customer Information</h3>
                @if($booking->booking_status != 'cancelled')
                    <a href="{{ route('bookings.edit', $booking) }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Name</p>
                    <p class="font-semibold">{{ $booking->customer_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Mobile</p>
                    <p class="font-semibold">{{ $booking->customer_mobile }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="font-semibold">{{ $booking->customer_email ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">ID Proof</p>
                    <p class="font-semibold">{{ ucfirst($booking->id_proof_type) }} - {{ $booking->id_proof_number }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-sm text-gray-600">Address</p>
                    <p class="font-semibold">{{ $booking->customer_address }}</p>
                </div>
                @if($booking->company_name)
                    <div>
                        <p class="text-sm text-gray-600">Company Name</p>
                        <p class="font-semibold">{{ $booking->company_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">GST Number</p>
                        <p class="font-semibold">{{ $booking->gst_number }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Booking Info -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Booking Information</h3>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm text-gray-600">Check-in</p>
                    <p class="font-semibold">{{ $booking->check_in->format('d M Y, h:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Check-out</p>
                    <p class="font-semibold">{{ $booking->check_out->format('d M Y, h:i A') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Number of Nights</p>
                    <p class="font-semibold">{{ $booking->number_of_nights }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Number of Adults</p>
                    <p class="font-semibold">{{ $booking->number_of_adults }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Number of Children</p>
                    <p class="font-semibold">{{ $booking->number_of_children }}</p>
                </div>
            </div>

            <div class="border-t pt-4">
                <p class="text-sm text-gray-600 mb-2">Booked Rooms</p>
                <div class="space-y-2">
                    @foreach($booking->bookingRooms as $br)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                            <div>
                                <span class="font-semibold">Room {{ $br->room->room_number }}</span>
                                <span class="text-sm text-gray-600 ml-2">({{ $br->room->roomType->name }})</span>
                            </div>
                            <span class="font-semibold text-blue-600">₹{{ number_format($br->room_price, 2) }}/night</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Extra Charges -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">Extra Charges</h3>
                @if($booking->booking_status != 'cancelled' && $booking->booking_status != 'checked_out')
                    <button onclick="document.getElementById('addChargeModal').classList.remove('hidden')"
                        class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-plus mr-1"></i> Add Charge
                    </button>
                @endif
            </div>

            @if($booking->extraCharges->count() > 0)
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs">Type</th>
                            <th class="px-4 py-2 text-left text-xs">Description</th>
                            <th class="px-4 py-2 text-left text-xs">Amount</th>
                            <th class="px-4 py-2 text-left text-xs">Date</th>
                            <th class="px-4 py-2 text-left text-xs">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($booking->extraCharges as $charge)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ ucfirst($charge->charge_type) }}</td>
                                <td class="px-4 py-2">{{ $charge->description }}</td>
                                <td class="px-4 py-2">₹{{ number_format($charge->amount, 2) }}</td>
                                <td class="px-4 py-2">{{ $charge->charge_date->format('d M Y') }}</td>
                                <td class="px-4 py-2">
                                    @if($booking->booking_status != 'cancelled')
                                        <form action="{{ route('extra-charges.destroy', $charge) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-500 text-center py-4">No extra charges</p>
            @endif
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Actions</h3>

            <div class="space-y-2">
                <a href="{{ route('bookings.invoice', $booking) }}" target="_blank"
                    class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded">
                    <i class="fas fa-file-invoice mr-2"></i> View Invoice
                </a>

                @if($booking->booking_status == 'confirmed')
                    <form action="{{ route('bookings.check-in', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                            <i class="fas fa-sign-in-alt mr-2"></i> Check In
                        </button>
                    </form>
                @endif

                @if($booking->booking_status == 'checked_in')
                    <form action="{{ route('bookings.check-out', $booking) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded">
                            <i class="fas fa-sign-out-alt mr-2"></i> Check Out
                        </button>
                    </form>
                @endif

                @if($booking->booking_status != 'cancelled' && $booking->booking_status != 'checked_out')
                    <button onclick="document.getElementById('cancelModal').classList.remove('hidden')"
                        class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                        <i class="fas fa-times mr-2"></i> Cancel Booking
                    </button>
                @endif
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Payment Summary</h3>

            <div class="space-y-2 mb-4">
                <div class="flex justify-between">
                    <span>Room Charges:</span>
                    <span class="font-semibold">₹{{ number_format($booking->room_charges, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>GST:</span>
                    <span class="font-semibold">₹{{ number_format($booking->gst_amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Service Tax:</span>
                    <span class="font-semibold">₹{{ number_format($booking->service_tax, 2) }}</span>
                </div>
                @if($booking->extra_charges > 0)
                    <div class="flex justify-between">
                        <span>Extra Charges:</span>
                        <span class="font-semibold">₹{{ number_format($booking->extra_charges, 2) }}</span>
                    </div>
                @endif
                <div class="flex justify-between border-t pt-2 text-lg">
                    <span class="font-bold">Total Amount:</span>
                    <span class="font-bold text-blue-600">₹{{ number_format($booking->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-green-600">
                    <span>Advance Paid:</span>
                    <span class="font-semibold">₹{{ number_format($booking->advance_payment, 2) }}</span>
                </div>
                <div class="flex justify-between text-red-600">
                    <span>Remaining:</span>
                    <span class="font-semibold">₹{{ number_format($booking->remaining_amount, 2) }}</span>
                </div>
            </div>

            @if($booking->remaining_amount > 0 && $booking->booking_status != 'cancelled')
                <button onclick="document.getElementById('paymentModal').classList.remove('hidden')"
                    class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    <i class="fas fa-money-bill mr-2"></i> Add Payment
                </button>
            @endif

            <div class="mt-4 pt-4 border-t">
                <div class="flex justify-between items-center">
                    <span>Payment Status:</span>
                    <span class="px-3 py-1 text-sm rounded
                        @if($booking->payment_status == 'paid') bg-green-100 text-green-800
                        @elseif($booking->payment_status == 'partial') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($booking->payment_status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Extra Charge Modal -->
<div id="addChargeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">Add Extra Charge</h3>
        <form action="{{ route('bookings.extra-charges', $booking) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Charge Type</label>
                <input type="text" name="charge_type" placeholder="e.g., Room Service, Laundry"
                    class="w-full px-3 py-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Description</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border rounded" required></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Amount (₹)</label>
                    <input type="number" name="amount" step="0.01" class="w-full px-3 py-2 border rounded" required>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Date</label>
                    <input type="date" name="charge_date" value="{{ date('Y-m-d') }}"
                        class="w-full px-3 py-2 border rounded" required>
                </div>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Add</button>
                <button type="button" onclick="document.getElementById('addChargeModal').classList.add('hidden')"
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Payment Modal -->
<div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4">Add Payment</h3>
        <form action="{{ route('bookings.payment', $booking) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Payment Amount (₹)</label>
                <input type="number" name="payment_amount" step="0.01"
                    max="{{ $booking->remaining_amount }}" class="w-full px-3 py-2 border rounded" required>
                <p class="text-sm text-gray-500 mt-1">Remaining: ₹{{ number_format($booking->remaining_amount, 2) }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Payment Mode</label>
                <select name="payment_mode" class="w-full px-3 py-2 border rounded" required>
                    <option value="cash">Cash</option>
                    <option value="card">Card</option>
                    <option value="upi">UPI</option>
                    <option value="bank_transfer">Bank Transfer</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Add Payment</button>
                <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')"
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Cancel Booking Modal -->
<div id="cancelModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full">
        <h3 class="text-lg font-semibold mb-4 text-red-600">Cancel Booking</h3>
        <form action="{{ route('bookings.cancel', $booking) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Cancellation Reason</label>
                <textarea name="cancellation_reason" rows="3" class="w-full px-3 py-2 border rounded" required></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">Refund Amount (₹)</label>
                <input type="number" name="refund_amount" step="0.01" value="0"
                    max="{{ $booking->advance_payment }}" class="w-full px-3 py-2 border rounded">
                <p class="text-sm text-gray-500 mt-1">Maximum: ₹{{ number_format($booking->advance_payment, 2) }}</p>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">Cancel Booking</button>
                <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')"
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Close</button>
            </div>
        </form>
    </div>
</div>
@endsection
